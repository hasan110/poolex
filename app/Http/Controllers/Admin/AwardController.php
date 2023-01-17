<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Award;
use Carbon\Carbon;

class AwardController extends Controller
{   
    public function list(Request $request)
    {
        $awards = Award::all();

        return Response::success($awards ,'لیست جوایز با موفقیت دریافت شد .');
    }

    public function add(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'count' => ['required'],
            'need_call_number' => ['required'],
            'type' => ['required']
        ]);

        $award = Award::create([
            'title'=>$request->title,
            'amount'=>$request->amount,
            'count'=>$request->count,
            'need_call_number'=>$request->need_call_number,
            'type'=>$request->type
        ]);

        if($request->hasFile('icon'))
        {
            $award->update([
                'icon' => $this->uploadFile($request->icon , 'awards')
            ]);
        }

        return Response::success($award , ' جایزه افزوده شد.');
    }

    public function get(Request $request)
    {
        $award = Award::find($request->id);
        return Response::success($award ,' جایزه  .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'count' => ['required'],
            'need_call_number' => ['required'],
            'type' => ['required']
        ]);
        $award = Award::find($request->id);

        $award->update([
            'title'=>$request->title,
            'amount'=>$request->amount,
            'count'=>$request->count,
            'need_call_number'=>$request->need_call_number,
            'type'=>$request->type
        ]);

        if($request->hasFile('new_icon'))
        {
            if($award->icon)
            {
                $this->deleteFile($award->icon);
            }

            $award->update([
                'icon' => $this->uploadFile($request->new_icon , 'awards')
            ]);
        }

        return Response::success($award ,' جایزه  .');
    }

    public function delete(Request $request)
    {
        $award = Award::find($request->id);

        $award->delete();

        return Response::success(null ,' جایزه حذف شد .');
    }
}
