<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Morilog\Jalali\Jalalian;
use App\Models\SlideShow;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class SlideShowController extends Controller
{
    public function get_slideshows(Request $request)
    {
        $list = SlideShow::where('type' , 'slideshow')->whereStatus(1)->get();

        return Response::success($list , 'لیست با موفقیت دریافت شد .');
    }

    public function get_movie_banners(Request $request)
    {
        $list = SlideShow::where('type' , 'movie_banner')->whereStatus(1)->get();

        return Response::success($list , 'لیست با موفقیت دریافت شد .');
    }

    public function get_movie_slideshows(Request $request)
    {
        $list = SlideShow::where('type' , 'movie_slideshow')->whereStatus(1)->get();

        return Response::success($list , 'لیست با موفقیت دریافت شد .');
    }

}
