<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Product;
use Carbon\Carbon;

class ProductController extends Controller
{   
    public function list(Request $request)
    {
        $list = Product::all();

        return Response::success($list ,'لیست محصولات با موفقیت دریافت شد .');
    }

    public function add(Request $request)
    {
        $request->validate([
            'price' => ['required'],
            'coin' => ['required'],
            'before_coin' => ['required']
        ]);

        if($request->priority){
            $this->change_priority();
        }

        $Product = Product::create($request->all());
        return Response::success($Product , ' محصول افزوده شد  .');
    }

    public function get(Request $request)
    {
        $Product = Product::find($request->id);
        return Response::success($Product ,' محصول  .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'price' => ['required'],
            'coin' => ['required'],
            'before_coin' => ['required']
        ]);
        $Product = Product::find($request->id);

        if($request->priority){
            $this->change_priority();
        }

        $Product->update($request->all());

        return Response::success($Product ,' محصول  .');
    }

    public function delete(Request $request)
    {
        $Product = Product::find($request->id);

        $Product->delete();

        return Response::success(null ,' محصول حذف شد .');
    }

    public function change_priority()
    {
        $all = Product::all();

        if($all)
        {
            foreach($all as $p){
                $p->update([
                    'priority' => 0
                ]);
            }
        }

        return true;
    }
}
