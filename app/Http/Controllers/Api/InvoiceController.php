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
use App\Models\Pay;
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
                    if($store->id != $product->store->id)
                    {
                        return Response::error(null , 'لطفا محصولات یک فروشگاه را در سبد خرید انتخاب نمایید.' , null , 400);
                    }
                }
            }
        }

        $invoice = new Invoice([
            'uuid'=>uniqid(),
            'user_id'=>$user->id,
            'seller_id'=>$store->user->id,
            'store_id'=>$store->id,
            'fullname'=>$request->fullname,
            'province'=>$request->province,
            'city'=>$request->city,
            'address'=>$request->address,
            'postal_code'=>$request->postal_code,
            'call_number'=>$request->call_number,
            'shipping_type'=>$request->shipping_type ? 0 : 1,
            'fullname'=>$request->fullname,
            'description'=>$request->description,
        ]);

        $total = 0;

        foreach($products as $one)
        {
            $cost = $one['cart_count'] * $one['price'];
            $total += $cost;
        }
        $shipping_cost = $this->settings()->stores_shipping_cost;
        $invoice->invoice_amount = $total;
        $invoice->shipping_cost = $shipping_cost;
        $invoice->total_amount = $total + $shipping_cost;
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

    public function user_order_details(Request $request)
    {
        $user = $this->getUserByToken($request);

        $invoice = Invoice::where('uuid' , $request->id)->where('user_id' , $user->id)->with(['user' , 'items' => function ($q){
            $q->with('store_product');
        } , 'store'])->first();

        if(!$invoice)
        {
            return Response::error(null , 'سفارش یافت نشد.' , null , 404);
        }

        $invoice['date'] = Jalalian::forge($invoice->created_at)->format('%Y/%m/%d');
        $invoice['time'] = Jalalian::forge($invoice->created_at)->format('H:i');
        $invoice['status_title'] = Invoice::getStatusTitle($invoice->status);

        return Response::success($invoice , 'اطلاعات با موفقیت دریافت شد .');
    }

    public function user_order_list(Request $request)
    {
        $user = $this->getUserByToken($request);

        $status = 1;
        if(isset($request->status) && $request->status == 'history')
        {
            $status = 0;
        }

        $list = Invoice::where('user_id' , $user->id);

        if($status){
            $list = $list->where('status' , '!=' , 5);
        }else{
            $list = $list->where('status' , 5);
        }

        $list = $list->with(['user' , 'items' , 'store'])->latest()->paginate();

        return Response::success($list , 'اطلاعات با موفقیت دریافت شد .');
    }

    public function pay_order(Request $request)
    {
        $validation = $this->validateData($request , [
            'order_id' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $user = $this->getUserByToken($request);

        $invoice = Invoice::where('uuid' , $request->order_id)->where('user_id' , $user->id)->first();

        if(!$invoice || $invoice->status != 1)
        {
            return Response::error(null , 'شناسه سفارش نامعتبر است.' , null , 404);
        }

        $today = Carbon::today()->format('Y-m-d');

        $token = md5(uniqid(microtime(), true));
        $description = 'پرداخت سفارش';
        $pay = Pay::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'amount' => $invoice->total_amount,
            'token' => $token,
            'description' => $description,
            'expire' => Carbon::now()->addMinutes(30)
        ]);

        $callback = route('pay.pay_order' , ['token'=>$pay->token]);

        $data = array(
            "merchant_id" => 'b90349e6-9dd2-4e5e-a3e7-6477f81ab6e4',
            "amount" => $pay->amount*10,
            "callback_url" => $callback,
            "description" => 'پرداخت سفارش',
            "metadata" => [ "email" => $user->email],
        );
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true, JSON_PRETTY_PRINT);
        curl_close($ch);

        if ($err) {
            $pay->update([
                'status'=>2,
                'pay_description'=>'خطا در ثبت تراکنش در درگاه.',
            ]);
            return Response::error(null , 'خطای اتصال به درگاه رخ داده است .' , null);
        } else {
            if (empty($result['errors'])) {
                if ($result['data']['code'] == 100) {
                    $link = 'https://www.zarinpal.com/pg/StartPay/' . $result['data']["authority"];
                    return Response::success($link , 'انتقال به درگاه ...');
                }
            } else {
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'خطا در ثبت تراکنش در درگاه.',
                ]);
                return Response::error(null , "خطای اتصال به درگاه (".$result['errors']['message'].") ." , null);
            }
        }
    }

    public function cash_pay_order(Request $request)
    {
        $validation = $this->validateData($request , [
            'order_id' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $user = $this->getUserByToken($request);

        $invoice = Invoice::where('uuid' , $request->order_id)->where('user_id' , $user->id)->with('items')->first();

        if(!$invoice || $invoice->status != 1)
        {
            return Response::error(null , 'شناسه سفارش نامعتبر است.' , null , 404);
        }

        if($user->cash < $invoice->total_amount)
        {
            return Response::error(null , 'اعتبار شما برای پرداخت این سفارش کافی نیست.' , null);
        }

        $user->update([
            'cash' => $user->cash - $invoice->total_amount
        ]);

        $invoice->update([
            'status'=>2
        ]);

        foreach ($invoice->items as $detail)
        {
            $product = StoreProduct::find($detail['store_product_id']);
            if($product)
            {
               $new_inventory = $product->inventory - $detail['count'];
               if($new_inventory < 1){
                   $new_inventory = 0;
               }
               $product->update([
                   'inventory'=>$new_inventory
               ]);
            }
        }

        return Response::success(null , 'سفارش با موفقیت پرداخت شد .');
    }

    // seller operations

    public function seller_order_list(Request $request)
    {
        $user = $this->getUserByToken($request);

        $status = 1;
        if(isset($request->status) && $request->status == 'new')
        {
            $status = 0;
        }

        $stores = Store::where('user_id' , $user->id)->pluck('id')->toArray();

        $list = Invoice::whereIn('store_id' , $stores);

        if($status){
            $list = $list->whereIn('status' , [4 , 5]);
        }else{
            $list = $list->whereNotIn('status' , [5 , 4]);
        }

        $list = $list->with(['user' , 'items' , 'store'])->latest()->paginate();

        return Response::success($list , 'اطلاعات با موفقیت دریافت شد .');
    }

    public function seller_order_details(Request $request)
    {
        $user = $this->getUserByToken($request);

        $invoice = Invoice::where('uuid' , $request->id)->where('seller_id' , $user->id)->with(['user' , 'items' => function ($q){
            $q->with('store_product');
        } , 'store'])->first();

        if(!$invoice)
        {
            return Response::error(null , 'سفارش یافت نشد.' , null , 404);
        }

        $invoice['date'] = Jalalian::forge($invoice->created_at)->format('%Y/%m/%d');
        $invoice['time'] = Jalalian::forge($invoice->created_at)->format('H:i');
        $invoice['status_title'] = Invoice::getStatusTitle($invoice->status);

        return Response::success($invoice , 'اطلاعات با موفقیت دریافت شد .');
    }

    public function seller_change_order_status(Request $request)
    {
        $validation = $this->validateData($request , [
            'status' => 'required'
        ]);
        if($validation){
            return Response::error(null , 'وضعیت سفارش را ارسال کنید.' , null);
        }

        $user = $this->getUserByToken($request);

        $invoice = Invoice::where('uuid' , $request->order_id)->where('seller_id' , $user->id)->with(['user' , 'items' => function ($q){
            $q->with('store_product');
        } , 'store'])->first();

        if(!$invoice)
        {
            return Response::error(null , 'سفارش یافت نشد.' , null , 404);
        }

        switch ((int) $request->status) {
            case 1:
                return $this->sellerConfirmOrder($invoice);
            case 3:
                return $this->readyToSend($invoice);
            case 5:
                return $this->cancelOrder($invoice);
            default:
                return Response::error(null , 'نمی توان این سفارش را تغییر وضعیت داد.' , null);
        }
    }

    public function sellerConfirmOrder($invoice){
        if($invoice->status != 0)
        {
            return Response::error(null , 'نمی توان این سفارش را تایید کرد.' , null);
        }

        $invoice->update([
            'status'=>1
        ]);

        return Response::success($invoice , 'تایید سفارش با موفقیت انجام شد .');
    }

    public function readyToSend($invoice){
        if($invoice->status != 2)
        {
            return Response::error(null , 'نمی توان این سفارش را آماده به ارسال کرد.' , null);
        }

        $invoice->update([
            'status'=>3
        ]);

        return Response::success($invoice , 'آماده به ارسال کردن سفارش با موفقیت انجام شد .');
    }

    public function cancelOrder($invoice){
        if($invoice->status != 0)
        {
            return Response::error(null , 'نمی توان این سفارش را انصرافی زد.' , null);
        }

        $invoice->update([
            'status'=>5
        ]);

        return Response::success($invoice , 'لغو سفارش با موفقیت انجام شد .');
    }
}
