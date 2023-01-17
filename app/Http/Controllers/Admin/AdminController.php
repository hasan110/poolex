<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function all(Request $request)
    {
        $list = Admin::latest()->paginate(50);

        return Response::success($list , 'لیست ادمین ها .');
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'user_name' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
            'star' => ['required']
        ]);

        $item = Admin::create([
            'name' => $request->name,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'star' => $request->star,
            'password' => Hash::make($request->password)
        ]);
        
        return Response::success($item , 'افزودن ادمین با موفقیت انجام شد.');
    }

    public function get(Request $request)
    {
        $item = Admin::find($request->id);

        $item['password'] = null;

        return Response::success($item , 'ادمین .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'name' => ['required'],
            'user_name' => ['required'],
            'email' => ['required'],
            'star' => ['required']
        ]);

        $item = Admin::find($request->id);
        
        $item->update([
            'name' => $request->name,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'star' => $request->star,
            'password' => $request->has('password') ? Hash::make($request->password) : $item->password
        ]);

        return Response::success($item , 'ویرایش ادمین با موفقیت انجام شد.');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => ['required']
        ]);

        Admin::find($request->id)->delete();

        return Response::success(null , 'حذف موفق');
    }

}
