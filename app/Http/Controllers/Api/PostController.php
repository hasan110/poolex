<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use Carbon\Carbon;

class PostController extends Controller
{
    public function get_helps(Request $request)
    {
        $data = Post::where('type',1)->where('status',1)->latest()->get();

        return Response::success($data , 'اطلاعات با موفقیت دریافت شد .');
    }
    public function get_sliders(Request $request)
    {
        $data = Post::where('type',2)->where('status',1)->latest()->get();

        return Response::success($data , 'اطلاعات با موفقیت دریافت شد .');
    }
    public function get_rules(Request $request)
    {
        $data = Post::where('type',3)->where('status',1)->latest()->get();

        return Response::success($data , 'اطلاعات با موفقیت دریافت شد .');
    }
    public function get_messages(Request $request)
    {
        $user = $this->getUserByToken($request);
        $data = Post::where('type',4)->where('status',1)->latest()->get();
        $user->user_plan->update([
            'last_message_seen'=>Carbon::now()->format('Y-m-d H:i:s')
        ]);

        return Response::success($data , 'اطلاعات با موفقیت دریافت شد .');
    }
}
