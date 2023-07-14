<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Category;
use Carbon\Carbon;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        $list = Category::latest();

        if($request->type){
            $list = $list->where('type',$request->type);
        }

        $list = $list->get();

        foreach($list as $item){
            $item['registered_at'] = Jalalian::forge($item->created_at)->format('%Y/%m/%d - %H:i');
        }
        return Response::success($list ,'لیست با موفقیت دریافت شد .');
    }

    public function add(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'icon' => ['image'],
            'type' => ['required']
        ]);
        $item = Category::create([
            'uuid'=>uniqid(),
            'title'=>$request->title,
            'type'=>$request->type,
            'color'=>$request->color ?? null,
            'priority'=>$request->priority,
        ]);

        if($request->hasFile('icon'))
        {
            $item->update([
                'icon' => $this->uploadFile($request->icon , 'categories')
            ]);
        }

        return Response::success($item ,'عملیات با موفقیت انجام شد .');
    }

    public function get(Request $request)
    {
        $item = Category::find($request->id);

        return Response::success($item ,'اطلاعات با موفقیت دریافت شد .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'type' => ['required']
        ]);
        $item = Category::find($request->id);

        $item->update([
            'title'=>$request->title,
            'type'=>$request->type,
            'color'=>$request->color ?? null,
            'priority'=>$request->priority
        ]);

        if($request->hasFile('new_icon'))
        {
            if($item->icon)
            {
                $this->deleteFile($item->icon);
            }
            $item->update([
                'icon' => $this->uploadFile($request->new_icon , 'categories')
            ]);
        }

        return Response::success($item ,'اطلاعات با موفقیت بروزرسانی شد .');
    }

    public function delete(Request $request)
    {
        $item = Category::find($request->id);

        if($item->icon)
        {
            $this->deleteFile($item->icon);
        }

        $item->delete();

        return Response::success(null ,'آیتم مورد نظر با موفقیت حذف شد .');
    }
}
