<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\View;
use Carbon\Carbon;

class ViewController extends Controller
{
    public function viewRanking(Request $request)
    {
        $today = Carbon::today();

        $views = View::where('type' , 'video')->with('user')->where('created_at' , '>=' , $today)->get();

        $list = [];
        foreach($views as $view)
        {
            if(!$view->user) { break; }

            $user = $view->user;

            if(!array_key_exists($view->user_id , $list))
            {
                $user->total_views = 1;
                $list[$view->user_id] = $user;
            }else{
                $list[$view->user_id]['total_views'] = $list[$view->user_id]['total_views'] + 1;
            }
        }

        $list = array_values($list);

        // $res = [];
        // foreach($list as $key => $item)
        // {
        //     User
        // }

        return Response::success($list , 'لیست بازدید ها با موفقیت دریافت شد .');
    }
}
