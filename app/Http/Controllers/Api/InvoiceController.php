<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function submit_order(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'products' => 'required|array',
            'products.*' => 'required',
            'fullname' => 'required',
            'province' => 'required',
            'city' => 'required',
            'address' => 'required',
            'postal_code' => 'required',
            'call_number' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $products = [];
        $store = null;
        foreach($request->products as $key=>$item)
        {
            $product = StoreProduct::whereHas('store')->where('uuid',$item['product_id'])->first();

            if($product)
            {
                if($item['count'] < 1)
                {
                    continue;
                }

                if($key === 0)
                {
                    $store = $product->store;
                }

                $product['cart_count'] = $item['count'];
                $products[] = $product;

                if($key !== 0)
                {
                    if($store->id !== $product->store->id)
                    {
                        return Response::error(null , 'لطفا محصولات یک فروشگاه را در سبد خرید انتخاب نمایید.' , null , 400);
                    }
                }
            }
        }

        $invoice = new Invoice([
            'uuid'=>uniqid(),
            'user_id'=>$user->id,
            'fullname'=>$request->fullname,
            'province'=>$request->province,
            'city'=>$request->city,
            'address'=>$request->address,
            'postal_code'=>$request->postal_code,
            'call_number'=>$request->call_number,
            'shipping_type'=>$request->shipping_type ? 0 : 1,
            'fullname'=>$request->fullname,
        ]);

        $total = 0;
        
        foreach($products as $one)
        {
            $cost = $one['cart_count'] * $one['price'];
            $total += $cost;
        }

        $invoice->invoice_amount = $total;
        $invoice->shipping_cost = $store->shipping_cost;
        $invoice->total_amount = $total + $store->shipping_cost;
        $invoice->save();
        
        foreach($products as $one)
        {
            $cost = $one['cart_count'] * $one['price'];
            $invoice_item = InvoiceItem::create([
                'invoice_id'=>$invoice->id,
                'store_product_id'=>$one['id'],
                'count'=>$one['cart_count'],
                'cost'=>$cost
            ]);
        }

        return Response::success($invoice , 'اطلاعات با موفقیت ثبت شد .');
    }
    
}
