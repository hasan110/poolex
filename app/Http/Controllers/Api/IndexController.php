<?php

namespace App\Http\Controllers\Api;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\SlideShow;
use App\Models\Category;
use App\Models\StoreProduct;
use App\Models\Store;
use App\Models\Chat;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function get_store_home_data(Request $request)
    {
        $cityCode = $request->header('cityCode');
        $slide_shows = SlideShow::where('type' , 'store_slideshow')->whereStatus(1)->get();

        $categories = Category::where('type' , 'store_category')->inRandomOrder()->take(10)->with(['store_products'=>function($q) use ($cityCode){
            $q->where('status' , 1)->inRandomOrder();
        }])->get();

        $latest_products = StoreProduct::where('status' , 1)->latest()->take(10)->get();

        $off_products = StoreProduct::where('status' , 1)->whereNotNull('discount')->inRandomOrder()->take(10)->get();

        $suggested_products = StoreProduct::where('status' , 1)->where('user_suggested' , 1)->inRandomOrder()->take(10)->get();

        $best_products = StoreProduct::where('status' , 1)->orderBy('views' , 'DESC')->take(20)->get();

        $data = [
            'slide_shows'=>$slide_shows,
            'categories'=>$categories,
            'latest_products'=>$latest_products,
            'best_products'=>$best_products,
            'off_products'=>$off_products,
            'suggested_products'=>$suggested_products
        ];

        return Response::success($data , 'اطلاعات با موفقیت دریافت شد .');
    }

    public function get_seller_global_data(Request $request)
    {
        $user = $this->getUserByToken($request);

        $stores = Store::where('user_id' , $user->id)->pluck('id')->toArray();

        $chat_has_unread_msg = Chat::whereIn('store_id' , $stores)->whereHas('messages' , function ($q){
            $q->where('sender' , 'user')->where('is_read' , 0);
        })->count();

        $new_orders_count = Invoice::whereIn('store_id' , $stores)->whereNotIn('status' , [5 , 4])->count();

        $data = [
            'new_orders_count'=>$new_orders_count,
            'chat_has_unread_msg'=>$chat_has_unread_msg,
        ];

        return Response::success($data , 'اطلاعات با موفقیت دریافت شد .');
    }

}
