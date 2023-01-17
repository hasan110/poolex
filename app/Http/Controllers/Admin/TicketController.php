<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Ticket;
use App\Models\TicketReply;

class TicketController extends Controller
{
    public $pagination = 50;
    
    public function list(Request $request)
    {
        $list = Ticket::whereNotNull('id');

        if($request->search_key){
            $search_key = $request->search_key;
            $list = $list->where(function($query) use ($search_key) {
                $query->where('subject','like', '%' . $search_key . '%')
                ->orWhere('text','like', '%' . $search_key . '%')
                ->orWhere('mobile_number','like', '%' . $search_key . '%')
                ->orWhere('email','like', '%' . $search_key . '%');
            });
        }

        if($request->has('status')){
            $status = $request->status;

            $list = $list->where('status' , $status);
        }

        $list = $list->with('user')->paginate($this->pagination);
        
        foreach($list as $item){
            $item['registered_at'] = Jalalian::forge($item->created_at)->format('%d / %m / %Y - H:i:s');
        }
        
        return Response::success($list ,'لیست با موفقیت دریافت شد .');
    }

    public function get(Request $request)
    {
        $item = Ticket::whereId($request->id)->with('user')->first();

        $item['date'] = Jalalian::forge($item->created_at)->format('%d / %m / %Y');
        $item['time'] = Jalalian::forge($item->created_at)->format('H:i:s');

        $replies = TicketReply::where('ticket_id' , $request->id)->with('user','admin')->latest()->get();

        if($replies){
            foreach($replies as $reply){
                $reply['date'] = Jalalian::forge($reply->created_at)->format('%d / %m / %Y');
                $reply['time'] = Jalalian::forge($reply->created_at)->format('H:i:s');
            }
        }
        $item['replies'] = $replies;

        return Response::success($item ,'اطلاعات با موفقیت دریافت شد .');
    }

    public function operate(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'status' => ['required']
        ]);
        $status = $request->status;
        $item = Ticket::find($request->id);

        $item->update([
            'status'=>$status,
        ]);

        return Response::success($item ,'تغییر وضعیت با موفقیت انجام شد .');
    }

    public function reply(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'reply' => ['required']
        ]);
        $item = Ticket::find($request->id);

        $item->replies()->create([
            'admin_id'=>auth()->user()->id,
            'text'=>$request->reply
        ]);

        $item->update([
            'status'=>1
        ]);

        return Response::success($item ,'پاسخ با موفقیت ثبت شد .');
    }
}
