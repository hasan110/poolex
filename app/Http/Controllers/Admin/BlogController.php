<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use Morilog\Jalali\Jalalian;

class BlogController extends Controller
{
    public int $pagination = 50;

    public function list(Request $request)
    {
        $list = Blog::query();

        if($request->search_key){
            $search_key = $request->search_key;
            $list = $list->where(function($query) use ($search_key) {
                $query->where('title','like', '%' . $search_key . '%')
                ->orWhere('short_description','like', '%' . $search_key . '%');
            });
        }

        if($request->has('sort'))
        {
            $sort = $request->sort;

            if($sort == 'newest'){
                $list = $list->orderBy('created_at' , 'desc');
            }
            elseif($sort == 'oldest'){
                $list = $list->orderBy('created_at' , 'asc');
            }
            elseif($sort == 'favorites'){
                $list = $list->orderBy('views' , 'desc');
            }
        }else{
            $list = $list->orderBy('created_at' , 'desc');
        }

        $list = $list->paginate($this->pagination);

        foreach($list as $item){
            $item['date'] = Jalalian::forge($item->created_at)->format('%d / %m / %Y');
        }

        return Response::success($list ,'لیست بلاگ ها با موفقیت دریافت شد .');
    }

    public function get(Request $request)
    {
        $blog = Blog::where('uuid' , $request->id)->first();

        if(!$blog)
        {
            return Response::error(null , 'بلاگ یافت نشد.' , null , 404);
        }

        return Response::success($blog ,'اطلاعات بلاگ با موفقیت دریافت شد .');
    }

    public function add(Request $request)
    {
        $request->validate([
            'thumbnail' => ['required','image','dimensions:width=240,height=300'],
            'banner' => ['required','image','dimensions:width=1200,height=900'],
            'title' => ['required','string','min:10'],
            'blog_keywords' => ['string'],
            'short_description' => ['required','string','min:130'],
            'blog_content' => ['required','string','min:250']
        ]);

        $blog = new Blog();
        $blog->uuid = uniqid();
        $blog->title = $request->title;
        $blog->short_description = $request->short_description;
        $blog->blog_content = $request->blog_content;
        $blog->blog_keywords = $request->blog_keywords;

        if($request->hasFile('thumbnail'))
        {
            $blog->thumbnail = $this->uploadFile($request->thumbnail , 'blogs');
        }
        if($request->hasFile('banner'))
        {
            $blog->banner = $this->uploadFile($request->banner , 'blogs');
        }

        $blog->save();

        return Response::success($blog ,'عملیات با موفقیت انجام شد .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'new_thumbnail' => ['image','dimensions:width=240,height=300'],
            'new_banner' => ['image','dimensions:width=1200,height=900'],
            'title' => ['required','string','min:15'],
            'blog_keywords' => ['string'],
            'short_description' => ['required','string','min:150'],
            'blog_content' => ['required']
        ]);

        $blog = Blog::where('id' , $request->id)->first();

        if(!$blog)
        {
            return Response::error(null , 'بلاگ یافت نشد.' , null , 404);
        }

        $blog->title = $request->title;
        $blog->short_description = $request->short_description;
        $blog->blog_content = $request->blog_content;
        $blog->blog_keywords = $request->blog_keywords;

        if($request->hasFile('new_thumbnail'))
        {
            $this->deleteFile($blog->thumbnail);
            $blog->thumbnail = $this->uploadFile($request->new_thumbnail , 'blogs');
        }
        if($request->hasFile('new_banner'))
        {
            $this->deleteFile($blog->banner);
            $blog->banner = $this->uploadFile($request->new_banner , 'blogs');
        }

        $blog->save();

        return Response::success($blog ,'عملیات با موفقیت انجام شد .');
    }

    public function delete(Request $request)
    {
        $blog = Blog::where('uuid' , $request->blog_id)->first();

        if(!$blog)
        {
            return Response::error(null , 'بلاگ یافت نشد.' , null , 404);
        }

        if($blog->thumbnail){
            $this->deleteFile($blog->thumbnail);
        }
        if($blog->banner){
            $this->deleteFile($blog->banner);
        }
        $blog->delete();

        return Response::success(null ,'بلاگ با موفقیت حذف شد .');
    }

}
