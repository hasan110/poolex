<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\SlideShow;
use App\Models\Category;
use App\Models\StoreProduct;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function get_store_home_data(Request $request)
    {
        // StoreProduct::create([
        //     'uuid'=>'tw5rt9q153f',
        //     'user_id'=>256,
        //     'store_id'=>1,
        //     'name'=>'محصول پنج مدل فاس',
        //     'slug'=>'product-5',
        //     'price'=>58000,
        //     'files'=>[
        //         ['path'=>'products/999dada93690575db5fb6417be081d477aaa672d_1603023104.jpg' , 'type'=>'image'],
        //         ['path'=>'products/999dada93690575db5fb6417be081d477aaa672d_1603023104.jpg' , 'type'=>'image'],
        //         ['path'=>'products/999dada93690575db5fb6417be081d477aaa672d_1603023104.jpg' , 'type'=>'image'],
        //     ],
        //     'description'=>'جنس :

        //     پارچه
            
        //     جنس زیره :
            
        //     پلی اورتان
            
        //     ویژگی‌های زیره :
            
        //     کاهش فشار وارده، مقاوم در برابر سایش
            
        //     نحوه بسته شدن کفش :
            
        //     بندی
            
        //     ویژگی‌های تخصصی کفش :
            
        //     قابلیت گردش هوا',
        //     'user_suggested'=>1,
        // ]);

        $slide_shows = SlideShow::where('type' , 'store_slideshow')->whereStatus(1)->get();
        
        $categories = Category::where('type' , 'store_category')->inRandomOrder()->take(10)->with('store_products')->get();
        
        $products = StoreProduct::where('status' , 1)->inRandomOrder()->take(20)->get();
        
        $latest_products = StoreProduct::where('status' , 1)->latest()->take(10)->get();
        
        $off_products = StoreProduct::where('status' , 1)->whereNotNull('discount')->orderBy('discount')->take(10)->get();
        
        $suggested_products = StoreProduct::where('status' , 1)->where('user_suggested' , 1)->take(10)->get();
        
        $best_products = StoreProduct::where('status' , 1)->latest()->take(10)->get();
        
        $data = [
            'slide_shows'=>$slide_shows,
            'categories'=>$categories,
            'products'=>$products,
            'latest_products'=>$latest_products,
            'best_products'=>$best_products,
            'off_products'=>$off_products,
            'suggested_products'=>$suggested_products
        ];

        return Response::success($data , 'اطلاعات با موفقیت دریافت شد .');
    }
}
