<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Blog;

class BlogController extends Controller
{
    public function get_blogs(Request $request)
    {
        $blogs = Blog::latest();
        if ($request->s && strlen($request->s) > 3)
        {
            $search_key = $request->s;
            $blogs = $blogs->where(function($query) use ($search_key) {
                $query->where('title','like', '%' . $search_key . '%')
                    ->orWhere('short_description','like', '%' . $search_key . '%');
            });
        }
        $blogs = $blogs->paginate(15);

        return Response::success($blogs , 'اطلاعات با موفقیت دریافت شد .');
    }

    public function get_blog(Request $request)
    {
        $blog = Blog::query()->where('uuid' , $request->id)->first();

        if(!$blog){
            return Response::error($blog , 'اطلاعات بلاگ یافت نشد' , null , 404);
        }

        // get previous blog id
        $previous = Blog::where('id', '<', $blog->id)->max('id');
        // get next blog id
        $next = Blog::where('id', '>', $blog->id)->min('id');

        if($previous) {
            $previous_blog = Blog::find($previous);
        }else{
            $previous_blog = null;
        }
        if($next) {
            $next_blog = Blog::find($next);
        }else{
            $next_blog = null;
        }

        $data = [
            'blog' => $blog,
            'previous_blog' => $previous_blog,
            'next_blog' => $next_blog
        ];
        return Response::success($data , 'اطلاعات با موفقیت دریافت شد .');
    }
}
