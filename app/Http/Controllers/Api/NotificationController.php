<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    public function notifications(Request $request)
    {
        $user = $this->getUserByToken($request);

        $list = $user->notifications()->latest()->get();
        $count =  $user->notifications->count();
        $data = [
            'list'=> $list,
            'count'=> $count
        ];
        return response()->json([
            'data'=> $data,
            'message'=> 'لیست اعلان ها .',
            'errors' => null,
        ],200);
    }
    public function notificationDelete(Request $request)
    {
        $user = $this->getUserByToken($request);

        $item = Notification::find($request->id);

        if($item && $user->id == $item->user_id){
            $item->delete();
        }else{
            return response()->json([
                'data'=> null,
                'message'=> 'حذف ناموفق',
                'errors' => null,
            ],200);
        }

        return response()->json([
            'data'=> null,
            'message'=> 'حذف موفق',
            'errors' => null,
        ],200);
    }
}