<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\SlideShow;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class SlideShowController extends Controller
{
    private $slideshow_types = ['slideshow','movie_slideshow','store_slideshow','movie_banner'];

    public function list(Request $request)
    {
        $list = SlideShow::all();

        return Response::success($list , 'لیست با موفقیت دریافت شد .');
    }

    public function add(Request $request)
    {
        $request->validate([
            'file' => ['required'],
            'type' => ['required'],
            'link_to' => ['required']
        ]);

        $type = $request->type;

        if(!in_array($type , $this->slideshow_types))
        {
            return Response::error(null , 'نوع اسلایدشو نامعتبر است.' , null ,  404);
        }

        $item = SlideShow::create([
            'uuid' => uniqid(),
            'internal_link' => $request->internal_link ? $request->internal_link : 0,
            'link_to' => $request->link_to,
            'description' => $request->description,
            'type' => $type
        ]);

        if($request->hasFile('file'))
        {
            $item->update([
                'file' => $this->uploadFile($request->file , 'slideshows')
            ]);
        }

        return Response::success($item , 'اسلایدشو با موفقیت افزوده شد .');
    }

    public function get(Request $request)
    {
        $item = SlideShow::find($request->id);
        if(!$item)
        {
            return Response::error(null , 'آیتم مورد نظر یافت نشد' , null ,  404);
        }
        $item->new_file = null;
        return Response::success($item , 'اطلاعات اسلایدشو با موفقیت دریافت شد .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'link_to' => ['required'],
            'type' => ['required']
        ]);

        $item = SlideShow::find($request->id);

        $type = $request->type;

        if(!in_array($type , $this->slideshow_types))
        {
            return Response::error(null , 'نوع اسلایدشو نامعتبر است.' , null ,  404);
        }

        $item->update([
            'internal_link' => $request->internal_link ? $request->internal_link : 0,
            'link_to' => $request->link_to,
            'description' => $request->description,
            'type' => $type
        ]);

        if($request->hasFile('new_file'))
        {
            if($item->file)
            {
                $this->deleteFile($item->file);
            }

            $item->update([
                'file' => $this->uploadFile($request->new_file , 'slideshows')
            ]);
        }

        return Response::success($item , 'اسلایدشو با موفقیت ویرایش شد .');
    }

    public function delete(Request $request)
    {
        $item = SlideShow::find($request->id);

        if($item->file)
        {
            $this->deleteFile($item->file);
        }

        $item->delete();

        return Response::success(null , 'اسلایدشو با موفقیت حذف شد .');
    }
}
