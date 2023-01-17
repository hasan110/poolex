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
use App\Models\Store;
use App\Models\Favorite;
use Carbon\Carbon;

class StoreController extends Controller
{
    public function get_store_data(Request $request)
    {
        $store = Store::where('uuid',$request->id)->with(['user','store_products' => function ($q){
            $q->where('status' , 1);
        }])->first();

        if(!$store)
        {
            return Response::error(null , 'اطلاعات مورد نظر یافت نشد .' , null);
        }

        return Response::success($store , 'اطلاعات با موفقیت دریافت شد .');
    }
    
}
