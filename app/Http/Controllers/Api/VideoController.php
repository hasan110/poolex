<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Category;
use App\Models\User;
use App\Models\Feedback;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class VideoController extends Controller
{
    public function get_videos(Request $request)
    {
        $validation = $this->validateData($request , [
            'type' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);

        $t = $request->type;
        if($t == 1 || $t == 2 || $t == 4)
        {
            $type = $request->type;
        }else{
            return Response::error(null , 'درخواست نامعتبر.' , null);
        }

        if($request->per_page)
        {
            $per_page = $request->per_page;
        }else{
            $per_page = 25;
        }

        $list = Video::where('status' , 1)->where('type' , $type)->orderBy('title' , 'ASC');

        if($t == 4)
        {
            $list = $list->get();
        }else{
            $list = $list->paginate($per_page);
        }
        
        foreach($list as $item)
        {
            $item['likes'] = $item->feedbacks()->where('type' , 'like')->count();
            $item['dislikes'] = $item->feedbacks()->where('type' , 'dislike')->count();
        }

        return Response::success($list , 'لیست ویدیو ها باموفقیت دریافت شد.');
    }

    public function get_video(Request $request)
    {
        $validation = $this->validateData($request , [
            'id' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);

        $item = Video::where('status' , 1)->where('uuid' , $request->id)->first();
        if(!$item)
        {
            return Response::error(null , 'ویدیو یافت نشد.' , null , 404);
        }

        $item['categories'] = $item->categories()->where('type' , 'category')->get();
        $item['genres'] = $item->categories()->where('type' , 'genre')->get();

        $item['episodes'] = $item->episodes()->orderBy('episode_number' , 'ASC')->get();
        $item['likes'] = $item->feedbacks()->where('type' , 'like')->count();
        $item['dislikes'] = $item->feedbacks()->where('type' , 'dislike')->count();

        $feedback = $item->feedbacks()->where('user_id' , $user->id)->first();
        if($feedback)
        {
            if($feedback->type == 'like')
            {
                $item['feedback_status'] = 1;
            }else{
                $item['feedback_status'] = 2;
            }
        }else{
            $item['feedback_status'] = 0;
        }

        return Response::success($item , 'اطلاعات ویدیو باموفقیت دریافت شد.');
    }

    public function search_videos(Request $request)
    {
        $user = $this->getUserByToken($request);
        if(!$request->search_key)
        {
            return Response::success(null , 'کلمه سرچ را وارد کنید.');
        }
        $search_key = $request->search_key;
        $list = Video::where('status' , 1)
        ->where(function($query) use ($search_key) {
            $query->where('title','like', '%' . $search_key . '%')
            ->orWhere('description','like', '%' . $search_key . '%');
        })->where(function($query){
            $query->where('type',1)
            ->orWhere('type',2);
        })->orderBy('title' , 'ASC')->paginate(25);

        foreach($list as $item)
        {
            $item['likes'] = $item->feedbacks()->where('type' , 'like')->count();
            $item['dislikes'] = $item->feedbacks()->where('type' , 'dislike')->count();
        }

        return Response::success($list , 'لیست ویدیو ها باموفقیت دریافت شد.');
    }

    public function get_categories(Request $request)
    {
        $user = $this->getUserByToken($request);
        $list = Category::orderBy('priority' , 'ASC')->where('type' , 'category')->get();

        foreach($list as $item){
            $videos = $item->videos()->inRandomOrder()->limit(10)->get();
            foreach($videos as $video)
            {
                $video['likes'] = $video->feedbacks()->where('type' , 'like')->count();
                $video['dislikes'] = $video->feedbacks()->where('type' , 'dislike')->count();
            }
            $item['videos'] = $videos;
        }

        return Response::success($list , 'لیست دسته بندی های ویدیو باموفقیت دریافت شد.');
    }

    public function get_videos_by_category(Request $request)
    {
        $validation = $this->validateData($request , [
            'id' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $user = $this->getUserByToken($request);

        $item = Category::where('uuid' , $request->id)->first();
        if(!$item){
            return Response::success(null , 'درخواست نامعتبر.');
        }

        if($request->per_page){
            $per_page = $request->per_page;
        }else{
            $per_page= 25;
        }

        $videos = $item->videos()->orderBy('title' , 'ASC')->paginate($per_page);
        foreach($videos as $video)
        {
            $video['likes'] = $video->feedbacks()->where('type' , 'like')->count();
            $video['dislikes'] = $video->feedbacks()->where('type' , 'dislike')->count();
        }
        $item['videos'] = $videos;

        return Response::success($item , 'لیست ویدیو ها باموفقیت دریافت شد.');
    }

    public function feedback_video(Request $request)
    {
        $validation = $this->validateData($request , [
            'video_id' => 'required',
            'type' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $user = $this->getUserByToken($request);

        $video = Video::where('status' , 1)->where('uuid' , $request->video_id)->first();
        if(!$video)
        {
            return Response::error(null , 'ویدیو یافت نشد.' , null , 404);
        }

        if($request->type == 'like' || $request->type == 'dislike')
        {
            $type = $request->type;
        }else{
            return Response::error(null , 'تایپ نامعتبر.' , null , 400);
        }

        $feedback = Feedback::where('user_id' , $user->id)->where('video_id' , $video->id)->first();
        if($feedback)
        {
            return Response::error(null , 'بازخورد شما قبلا ثبت شده است.' , null , 400);
        }

        $user->feedbacks()->create([
            'video_id'=>$video->id,
            'type'=>$type,
        ]);

        return Response::success(null , 'بازخورد ثبت شد.');
    }
}
