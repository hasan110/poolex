<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\File;

class TicketController extends Controller
{
    public function tickets(Request $request)
    {
        $user = $this->getUserByToken($request);

        $tickets = $user->tickets()->latest()->get();

        if($tickets){
            foreach($tickets as $item){
                $item['shamsi_created_at'] = Jalalian::forge($item->created_at)->format('%Y/%m/%d');
            }
        }

        return Response::success($tickets , 'لیست تیکت ها با موفقیت دریافت شد .');
    }
    public function add_ticket(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'subject' => 'required',
            'mobile_number' => 'required',
            'text' => 'required',
        ]);
        if($validation){
            return $validation;
        }

        $ticket = $user->tickets()->create([
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'subject' => $request->subject,
            'text' => $request->text,
            'status' => 0
        ]);

        if($request->hasFile('file')){
            $ticket->update([
                'file' => $this->uploadFile($request->file , 'users/'.$user->id),
            ]);
        }
        return Response::success($ticket , 'ثبت تیکت با موفقیت انجام شد .');
    }
    public function get_ticket(Request $request)
    {
        $user = $this->getUserByToken($request);

        $item = $user->tickets()->whereId($request->id)->first();

        if(!$item){
            return Response::error(null , 'تیکت مورد نظر یافت نشد .' , null);
        }
        
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
        return Response::success($item , 'اطلاعات تیکت با موفقیت دریافت شد .');
    }
    public function reply_ticket(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'id' => 'required',
            'text' => 'required',
        ]);
        if($validation){
            return $validation;
        }

        $item = Ticket::find($request->id);

        $item->replies()->create([
            'user_id'=>$user->id,
            'text'=>$request->text
        ]);

        $item->update([
            'status'=>0
        ]);

        return Response::success($item , 'پاسخ تیکت با موفقیت ثبت شد .');
    }
}
