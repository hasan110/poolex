<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Morilog\Jalali\Jalalian;
use App\Models\UserBankAccount;

class AccountController extends Controller
{
    public function OperateUserBankAccount(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'status' => ['required']
        ]);
        $status = $request->status;
        $item = UserBankAccount::find($request->id);
        $user = $item->user;
        if(!$status){

            $item->delete();

            $item = null;

            $reject_reason = $request->reject_reason;
        }else{

            $item->update([
                'status'=>$status
            ]);
        }

        return Response::success($item , 'کارت بانکی شما تغییر وضعیت داده شد .');
    }
}
