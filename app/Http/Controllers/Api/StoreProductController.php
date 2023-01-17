<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Feedback;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\StoreProduct;
use App\Models\Favorite;
use App\Models\Store;
use Carbon\Carbon;

class StoreProductController extends Controller
{
    public function get_product_data(Request $request)
    {
        $user = $this->getUserByToken($request);

        $product = StoreProduct::where('uuid',$request->id)->first();

        if(!$product)
        {
            return Response::error(null , 'اطلاعات مورد نظر یافت نشد .' , null);
        }

        $related = ProductCategory::where('store_product_id' , $product->id)->pluck('category_id')->toArray();
        $related_categories = ProductCategory::whereIn('category_id' , $related)->where('store_product_id' , '!=' , $product->id)->take(20)->pluck('store_product_id')->toArray();
        $final_same_products = [];
        foreach(array_unique($related_categories) as $item)
        {
            $store_product = StoreProduct::whereId($item)->first();
            
            if($store_product)
            {
                $final_same_products[] = $store_product;
            }
        }
        
        $product['same_products'] = $final_same_products;

        $comments = Feedback::where('product_id' , $product->id)->where('type' , 'comment')->whereStatus(1)->get();
        $final_comments_list = [];
        foreach($comments as $item)
        {
            $comment = [];
            $comment['text'] = $item->text;
            $comment['stars'] = $item->stars;
            $comment['user'] = $item->user->fullname ?? 'بی نام';
            $final_comments_list[] = $comment;
        }

        $product['is_favorite'] = 0;
        
        if($user){
            $favorite = Favorite::where('user_id' , $user->id)->where('store_product_id' , $product->id)->first();
            if($favorite){
                $product['is_favorite'] = 1;
            }
        }
        
        $product['comments'] = $final_comments_list;

        return Response::success($product , 'اطلاعات با موفقیت دریافت شد .');
    }
    
    public function favorites(Request $request)
    {
        $user = $this->getUserByToken($request);

        $products = $user->favorite_products;
        
        return Response::success($products , 'لیست محصولات مورد علاقه دریافت شد .');
    }

    public function toggle_favorite(Request $request)
    {
        $user = $this->getUserByToken($request);

        $product = StoreProduct::where('uuid',$request->product_id)->first();

        if(!$product)
        {
            return Response::error(null , 'محصول مورد نظر یافت نشد .' , null);
        }

        $favorite = Favorite::where('user_id' , $user->id)->where('store_product_id' , $product->id)->first();
        if($favorite){
            $favorite->delete();
        }else{
            Favorite::create([
                'user_id'=>$user->id,
                'store_product_id'=>$product->id
            ]);
        }
        
        return Response::success(null , 'موفق .');
    }

    public function add_comment(Request $request)
    {
        $validation = $this->validateData($request , [
            'text' => 'required',
            'stars' => 'required',
            'product_id' => 'required',
        ]);
        if($validation){
            return $validation;
        }

        $user = $this->getUserByToken($request);

        $product = StoreProduct::where('uuid',$request->product_id)->first();

        if(!$product)
        {
            return Response::error(null , 'محصول مورد نظر یافت نشد .' , null);
        }

        $feedback = Feedback::create([
            'user_id'=>$user->id,
            'product_id'=>$product->id,
            'type'=>'comment',
            'text'=>$request->text,
            'stars'=>$request->stars,
            'status'=>0
        ]);
        
        return Response::success(null , 'موفق .');
    }

    public function get_cart_products(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'products' => 'required|array',
            'products.*' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $products = [];
        
        foreach($request->products as $item)
        {
            $product = StoreProduct::where('uuid',$item['product_id'])->first();

            if($product)
            {
                $product['cart_count'] = $item['count'];
                $products[] = $product;
            }
        }
        
        return Response::success($products , 'موفق .');
    }

    // seller panel

    public function store_list(Request $request)
    {
        $user = $this->getUserByToken($request);

        $stores = Store::where([
            'user_id'=> $user->id
        ])->get();

        return Response::success($stores , 'فروشگاه های شما با موفقیت دریافت شد.');
    }

    public function store_data(Request $request , $id)
    {
        $user = $this->getUserByToken($request);

        $store = Store::where([
            'uuid'=> $id,
            'user_id'=> $user->id
        ])->with(['store_products' => function ($q){
            $q->orderBy('id' , 'DESC');
        }])->first();

        return Response::success($store , 'فروشگاه با موفقیت دریافت شد.');
    }

    public function add_store(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'banner' => 'required|image',
            'name' => 'required',
            'address' => 'required',
            'owner_name' => 'required',
            'description' => 'required',
            'shipping_time' => 'required',
            'shipping_cost' => 'required|numeric'
        ]);
        if ($validation) { return $validation; }

        if($request->shipping_time == 1)
        {
            $s_time = 'یک روز کاری';
        }else if($request->shipping_time == 2){
            $s_time = 'دو روز کاری';
        }else{
            $s_time = 'سه روز کاری';
        }

        if($request->hasFile('banner'))
        {
            $banner = $this->uploadFile($request->banner , 'stores');
        }else{
            $banner = null;
        }

        $store = Store::create([
            'uuid'=> uniqid(),
            'user_id'=> $user->id,
            'name'=> $request->name,
            'owner_name'=> $request->owner_name,
            'banner'=> $banner,
            'address'=> $request->address,
            'description'=> $request->description,
            'shipping_time'=> $s_time,
            'shipping_cost'=> $request->shipping_cost
        ]);

        return Response::success($store , 'فروشگاه شما با موفقیت ثبت شد. منتظر تایید ادمین باشید.');
    }

    public function add_product(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'images' => 'required|array',
            'images.*' => 'required',
            'name' => 'required',
            'description' => 'required',
            'inventory' => 'required',
            'price' => 'required|numeric',
            'store_id' => 'required'
        ]);
        if ($validation) { return $validation; }

        $store = Store::where([
            'uuid'=> $request->store_id,
            'user_id'=> $user->id
        ])->first();

        if(!$store)
        {
            return Response::error(null , 'فروشگاه یافت نشد .' , null);
        }

        $files = [];

        if($request->has('images'))
        {
            foreach($request->images as $file)
            {
                $img = $this->uploadFile($file , 'products/'.$store->id);
                $item = [
                    'path'=>$img,
                    'type'=>'image'
                ];
                $files[] = $item;
            }
        }

        $slug = str_replace(' ' , '-' , $request->name);

        $product = StoreProduct::create([
            'uuid'=> uniqid(),
            'user_id'=> $user->id,
            'store_id'=> $store->id,
            'name'=> $request->name,
            'slug'=> $slug,
            'price'=> $request->price,
            'inventory'=> $request->inventory,
            'files'=> $files,
            'status'=> 0,
            'description'=> $request->description
        ]);

        return Response::success($product , 'محصول شما با موفقیت ثبت شد. منتظر تایید ادمین باشید.');
    }

}
