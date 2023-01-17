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

class CategoryController extends Controller
{
    public function get_category_products(Request $request)
    {
        $category = Category::where('uuid',$request->id)->with('store_products')->first();

        if(!$category)
        {
            return Response::error(null , 'اطلاعات مورد نظر یافت نشد .' , null);
        }

        return Response::success($category , 'اطلاعات با موفقیت دریافت شد .');
    }
}
