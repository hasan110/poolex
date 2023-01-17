<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Post;
use Carbon\Carbon;

class PostController extends Controller
{   
    public function list(Request $request)
    {
        if($request->type)
        {
            $type = $request->type;
        }else{
            $type = 1;
        }

        $post = Post::where('type' , $type)->get();

        foreach($post as $item){
            if(strlen($item->description) > 50){
                $item->description = mb_substr($item->description , 0 , 50) . '...';
            }
            
            $item['create'] = Jalalian::forge($item->created_at)->format('H:i - %Y/%m/%d');
            $item['update'] = Jalalian::forge($item->updated_at)->format('H:i - %Y/%m/%d');
        }

        return Response::success($post ,'لیست با موفقیت دریافت شد .');
    }

    public function add(Request $request)
    {
        $request->validate([
            'description' => ['required'],
            'type' => ['required']
        ]);
        $post = Post::create($request->all());
        
        return Response::success($post ,'عملیات با موفقیت انجام شد .');
    }

    public function get(Request $request)
    {
        $post = Post::find($request->id);
        
        return Response::success($post ,'اطلاعات با موفقیت دریافت شد .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'description' => ['required'],
            'type' => ['required']
        ]);
        $post = Post::find($request->id);

        $post->update($request->all());

        return Response::success($post ,'اطلاعات با موفقیت بروزرسانی شد .');
    }

    public function delete(Request $request)
    {
        $post = Post::find($request->id);

        $post->delete();

        return Response::success(null ,'آیتم مورد نظر با موفقیت حذف شد .');
    }
}
