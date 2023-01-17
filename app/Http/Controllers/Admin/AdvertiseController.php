<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Advertise;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class AdvertiseController extends Controller
{
    public function list(Request $request)
    {
        $advertise = Advertise::all();

        foreach($advertise as $item){
            if(strlen($item->description) > 50){
                $item->description = mb_substr($item->description , 0 , 30) . '...';
            }
        }

        return Response::success($advertise , 'لیست تبلیغ ها با موفقیت دریافت شد .');
    }

    public function add(Request $request)
    {
        $request->validate([
            'picture' => ['required' , 'image'],
            'title' => ['required'],
            'type' => ['required'],
            'description' => ['required'],
        ]);

        $advertise = Advertise::create([
            'title' => $request->title,
            'number' => $request->number,
            'instagram_id' => $request->instagram_id,
            'link' => $request->link,
            'two_step' => $request->two_step ? 1 : 0,
            'description' => $request->description,
            'type' => $request->type,
            'second_ad' => $request->second_ad ? $request->second_ad : 1,
            'video' => $request->video ? $request->video : null
        ]);

        if($request->hasFile('picture'))
        {
            $advertise->update([
                'picture' => $this->uploadFile($request->picture , 'advertises')
            ]);
        }

        // if($request->hasFile('video'))
        // {
        //     $advertise->update([
        //         'video' => $this->uploadFile($request->video , 'advertises')
        //     ]);
        // }

        return Response::success($advertise , 'تبلیغ با موفقیت افزوده شد .');
    }

    public function get(Request $request)
    {
        $advertise = Advertise::find($request->id);

        if(!$advertise)
        {
            return Response::error(null , 'آیتم مورد نظر یافت نشد' , null ,  404);
        }

        $advertise->new_picture = null;
        $advertise->new_video = null;

        return Response::success($advertise , 'اطلاعات تبلیغ با موفقیت دریافت شد .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'type' => ['required'],
            'description' => ['required'],
        ]);

        $advertise = Advertise::find($request->id);

        $advertise->update([
            'title' => $request->title,
            'number' => $request->number,
            'instagram_id' => $request->instagram_id,
            'link' => $request->link,
            'two_step' => $request->two_step ? 1 : 0,
            'description' => $request->description,
            'type' => $request->type,
            'second_ad' => $request->second_ad ? $request->second_ad : 1,
            'video' => $request->video ? $request->video : null
        ]);

        if($request->hasFile('new_picture'))
        {
            if($advertise->picture)
            {
                File::delete(public_path().'/uploads/'.$advertise->picture);
            }

            $advertise->update([
                'picture' => $this->uploadFile($request->new_picture , 'advertises')
            ]);
        }

        // if($request->hasFile('new_video'))
        // {
        //     if($advertise->video)
        //     {
        //         File::delete(public_path().'/uploads/'.$advertise->video);
        //     }
        //     $advertise->update([
        //         'video' => $this->uploadFile($request->new_video , 'advertises')
        //     ]);
        // }

        return Response::success($advertise , 'تبلیغ با موفقیت ویرایش شد .');
    }

    public function delete(Request $request)
    {
        $advertise = Advertise::find($request->id);

        if($advertise->picture)
        {
            File::delete(public_path().'/uploads/'.$advertise->picture);
        }
        if($advertise->video)
        {
            File::delete(public_path().'/uploads/'.$advertise->video);
        }

        $advertise->delete();

        return Response::success(null , 'تبلیغ با موفقیت حذف شد .');
    }

    public function renewAll(Request $request)
    {
        $advertises = Advertise::all();

        foreach ($advertises as $item)
        {
            $item->update([
                'status' => 2
            ]);
            $item->update([
                'status' => 1
            ]);
        }

        $users = User::where('ad_keys' , '>' , 0)->get();
        foreach ($users as $item)
        {
            if($item->ad_keys)
            {
                $item->update([
                    'ad_keys' => 0
                ]);
            }
        }

        return Response::success(null , '');
    }
}
