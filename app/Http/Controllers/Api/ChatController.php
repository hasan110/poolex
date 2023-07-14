<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        $list = Chat::where('user_id' , $user->id)->with(['store','messages' => function ($q){
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

    // seller

    public function getSellerChatList(Request $request)
    {
        $user = $this->getUserByToken($request);

        $stores = Store::where('user_id' , $user->id)->pluck('id')->toArray();
        
        $list = Chat::whereIn('store_id' , $stores)->with(['store','messages' => function ($q){
            $q->orderBy('id' , 'DESC');
        },'user' => function ($q){
            $q->select('id' , 'fullname');
        }])->paginate(25);
        
        foreach($list as $item)
        {
            $unread = 0;
            foreach($item->messages as $message)
            {
                if(!$message->is_read && $message->sender == 'user')
                {
                    $unread++;
                }
            }
            $item['unread_messages'] = $unread;
        }

        return Response::success($list , 'اطلاعات با موفقیت دریافت شد .');
    }

    public function getSellerChatDetails(Request $request)
    {
        $user = $this->getUserByToken($request);

        $stores = Store::where('user_id' , $user->id)->pluck('id')->toArray();

        $item = Chat::where('id' , $request->chat_id)->whereIn('store_id' , $stores)->with('messages')->first();

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
            if($msg->sender == 'user' && !$msg->is_read)
            {
                $msg->update([
                    'is_read'=>1
                ]);
            }
        }

        return Response::success($item , 'جزییات چت .');
    }

    public function sendSellerChatMessage(Request $request)
    {
        $user = $this->getUserByToken($request);
        $validation = $this->validateData($request , [
            'message' => 'required',
            'chat_id' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $item = Chat::where('id' , $request->chat_id)->first();
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
            'sender'=>'seller',
            'is_read'=>1,
        ]);

        $item->update([
            'updated_at' => Carbon::now()
        ]);

        return Response::success($item , 'پیام جدید در چت .');
    }
}
