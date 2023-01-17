<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VerifyCode;
use App\Models\User;
use App\Models\Chat;
use App\Models\Store;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class ChatController extends Controller
{
    public function getChatList(Request $request)
    {
        $user = $this->getUserByToken($request);

        $list = Chat::where('user_id' , $user->id)->with(['messages' => function ($q){
            $q->orderBy('id' , 'DESC');
        }])->paginate(25);
        
        foreach($list as $item)
        {
            $unread = 0;
            foreach($item->messages as $message)
            {
                if(!$message->is_read && $message->sender == 'seller')
                {
                    $unread++;
                }
            }
            $item['unread_messages'] = $unread;
        }

        return response()->json([
            'data'=> $list,
            'message'=> 'لیست چت .',
            'errors' => null,
        ],200);
    }

    public function getChatDetails(Request $request)
    {
        $user = $this->getUserByToken($request);

        $item = Chat::where('id' , $request->chat_id)->where('user_id' , $user->id)->with('messages')->first();

        if(!$item)
        {
            return  response()->json([
                'data'=> null,
                'message'=> 'اطلاعات مورد نظر یافت نشد',
                'errors' => null,
            ],404);
        }

        foreach($item->messages as $msg)
        {
            if($msg->sender == 'seller' && !$msg->is_read)
            {
                $msg->update([
                    'is_read'=>1
                ]);
            }
        }

        return response()->json([
            'data'=> $item,
            'message'=> 'جزییات چت .',
            'errors' => null,
        ],200);
    }

    public function sendChatMessage(Request $request)
    {
        $user = $this->getUserByToken($request);
        $validation = $this->validateData($request , [
            'message' => 'required',
            'chat_id' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $item = Chat::where('id' , $request->chat_id)->where('user_id' , $user->id)->first();
        if(!$item)
        {
            return response()->json([
                'data'=> null,
                'message'=> 'اطلاعات مورد نظر یافت نشد',
                'errors' => null,
            ],404);
        }

        $item->messages()->create([
            'text'=>$request->message,
            'sender'=>'user',
        ]);

        $item->update([
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'data'=> $item,
            'message'=> 'پیام جدید در چت .',
            'errors' => null,
        ],200);
    }

    public function newChat(Request $request)
    {
        $user = $this->getUserByToken($request);
        $validation = $this->validateData($request , [
            'store_id' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $store = Store::where('uuid' , $request->store_id)->first();
        if(!$store)
        {
            return response()->json([
                'data'=> null,
                'message'=> 'اطلاعات مورد نظر یافت نشد',
                'errors' => null,
            ],404);
        }

        $chat = Chat::where([
            'user_id'=>$user->id,
            'store_id'=>$store->id
        ])->first();

        if(!$chat)
        {
            $chat = Chat::create([
                'user_id'=>$user->id,
                'store_id'=>$store->id,
                'status'=>1,
            ]);
        }

        return response()->json([
            'data'=> $chat,
            'message'=> 'شروع چت با فروشگاه .',
            'errors' => null,
        ],200);
    }
}
