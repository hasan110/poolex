<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class VideoController extends Controller
{
    public $pagination = 50;

    public function list(Request $request)
    {
        $list = Video::whereNotNull('id');

        if($request->search_key){
            $search_key = $request->search_key;
            $list = $list->where(function($query) use ($search_key) {
              $query->where('title','like', '%' . $search_key . '%');
            });
        }

        if($request->has('sort'))
        {
            $sort = $request->sort;

            if($sort == 'name'){
                $list = $list->orderBy('title' , 'asc');
            }
            elseif($sort == 'newest'){
                $list = $list->orderBy('created_at' , 'desc');
            }
            elseif($sort == 'oldest'){
                $list = $list->orderBy('created_at' , 'asc');
            }
            elseif($sort == 'film'){
                $list = $list->where('type' , 1);
            }
            elseif($sort == 'series'){
                $list = $list->where('type' , 2);
            }
            elseif($sort == 'episode'){
                $list = $list->where('type' , 3);
            }
        }else{
            $list = $list->orderBy('created_at' , 'desc');
        }

        $list = $list->with(['episodes' , 'serial']);

        $list = $list->paginate($this->pagination);

        foreach($list as $item){
            $item['create'] = Jalalian::forge($item->created_at)->format('H:i - %Y/%m/%d');
        }

        return Response::success($list , 'لیست ویدیو ها با موفقیت دریافت شد .');
    }

    public function add(Request $request)
    {
        // return Response::error($request->request , 'آیتم مورد نظر یافت نشد' , null ,  404);
        $request->validate([
            'poster' => ['image'],
            'title' => ['required'],
            'description' => ['required'],
            'translate_status' => ['required'],
            'type' => ['required']
        ]);

        if($request->type == 1){
            $request->validate([
                'poster' => ['required'],
                'file' => ['required'],
            ]);
        }elseif($request->type == 2){
            $request->validate([
                'poster' => ['required'],
            ]);
        }elseif($request->type == 3){
            $request->validate([
                'file' => ['required'],
                'video_id' => ['required'],
                'episode_number' => ['required']
            ]);
        }

        $item = Video::create([
            'uuid' => uniqid(),
            'title' => $request->title,
            'video_id' => $request->video_id ? $request->video_id : null,
            'type' => $request->type,
            'translate_status' => $request->translate_status,
            'episode_number' => $request->episode_number,
            'file' => $request->file,
            'rate' => $request->rate,
            'complete_link' => $request->complete_link ? $request->complete_link : 0,
            'description' => $request->description
        ]);

        if($request->hasFile('poster'))
        {
            $item->update([
                'poster' => $this->uploadFile($request->poster , 'videos/poster/'.$item->uuid)
            ]);
        }

        if($request->categories)
        {
            $item->categories()->attach(explode(',' , $request->categories));
        }

        if($request->genres)
        {
            $item->categories()->attach(explode(',' , $request->genres));
        }

        return Response::success($item , 'ویدیو با موفقیت افزوده شد .');
    }

    public function get(Request $request)
    {
        $item = Video::find($request->id);

        if(!$item)
        {
            return Response::error(null , 'آیتم مورد نظر یافت نشد' , null ,  404);
        }

        $item->new_poster = null;

        if($item->categories){
            $item->edit_categories = $item->categories()->where('type' , 'category')->pluck('id')->toArray();
            $item->edit_genres = $item->categories()->where('type' , 'genre')->pluck('id')->toArray();
        }else{
            $item->edit_categories = [];
            $item->edit_genres = [];
        }

        return Response::success($item , 'اطلاعات ویدیو با موفقیت دریافت شد .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'poster' => ['image'],
            'title' => ['required'],
            'translate_status' => ['required'],
            'description' => ['required']
        ]);

        $item = Video::find($request->id);

        if($item->type == 1 || $item->type == 3){
            $request->validate([
                'file' => ['required']
            ]);
            $file = $request->file;
        }else{
            $file = null;
        }

        $item->update([
            'title' => $request->title,
            'file' => $file,
            'video_id' => $request->video_id,
            'rate' => $request->rate,
            'episode_number' => $request->episode_number,
            'translate_status' => $request->translate_status,
            'complete_link' => $request->complete_link ? $request->complete_link : 0,
            'description' => $request->description
        ]);

        if($request->hasFile('new_poster'))
        {
            if($item->poster)
            {
                $this->deleteFile($item->poster);
            }

            $item->update([
                'poster' => $this->uploadFile($request->new_poster , 'videos/poster/'.$item->uuid)
            ]);
        }

        $item->categories()->detach();

        if($request->categories){
            $item->categories()->attach(explode(',' , $request->categories));
        }

        if($request->genres){
            $item->categories()->attach(explode(',' , $request->genres));
        }

        return Response::success($item , 'ویدیو با موفقیت ویرایش شد .');
    }

    public function delete(Request $request)
    {
        $item = Video::find($request->id);

        if($item->poster)
        {
            $this->deleteFile($item->poster);
        }
        if($item->file)
        {
            $this->deleteFile($item->file);
        }

        if($item->episodes){
            foreach($item->episodes as $episode){
                if($episode->poster)
                {
                    $this->deleteFile($episode->poster);
                }
                if($episode->file)
                {
                    $this->deleteFile($episode->file);
                }
            }
        }
        $item->categories()->detach();

        $item->delete();

        return Response::success(null , 'ویدیو با موفقیت حذف شد .');
    }

    public function serials(Request $request)
    {
        $list = Video::where('type',2)->where('status',1)->get();

        return Response::success($list , 'لیست سریال ها با موفقیت دریافت شد .');
    }
}
