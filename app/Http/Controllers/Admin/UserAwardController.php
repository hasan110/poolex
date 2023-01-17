<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\UserAward;
use Carbon\Carbon;

class UserAwardController extends Controller
{
    public $pagination = 50;

    public function list(Request $request)
    {
        $list = UserAward::whereNotNull('id');

        if($request->has('sort'))
        {
            $sort = $request->sort;

            if($sort == 'newest'){
                $list = $list->orderBy('created_at' , 'desc');
            }
            elseif($sort == 'oldest'){
                $list = $list->orderBy('created_at' , 'asc');
            }
            elseif($sort == 'confirmed'){
                $list = $list->where('status' , 1);
            }
            elseif($sort == 'rejected'){
                $list = $list->where('status' , 2);
            }
            elseif($sort == 'pending'){
                $list = $list->where('status' , 0);
            }
        }else{
            $list = $list->orderBy('created_at' , 'desc');
        }

        $list = $list->with('user')->with('award')->paginate($this->pagination);

        foreach($list as $item){
            $item['registered_at'] = Jalalian::forge($item->created_at)->format('%Y/%m/%d - %H:i');
        }

        return Response::success($list , 'لیست جوایز با موفقیت دریافت شد .');
    }

    public function get(Request $request)
    {
        $item = Harvest::whereId($request->id)->with('user')->first();

        $item['date'] = Jalalian::forge($item->created_at)->format('%d / %m / %Y');
        $item['time'] = Jalalian::forge($item->created_at)->format('H:i:s');
        // $item['amount'] = number_format($item->amount);
        if($item->wallet_id){
            $wallet = Wallet::find($item->wallet_id);

            if($wallet){
                $item['wallet_address'] = $wallet->address;
            }
        }

        if($item->user_bank_account_id){
            $account = UserBankAccount::find($item->user_bank_account_id);

            if($account){
                $item['user_bank_account'] = $account;
            }
        }else
        {
            $item['user_bank_account'] = null;
        }

        return Response::success($item , 'اطلاعات برداشت با موفقیت دریافت شد .');
    }

    public function operate(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'status' => ['required']
        ]);

        $status = $request->status;
        $item = UserAward::find($request->id);
        $user = $item->user;

        $item->update([
            'status'=>$status
        ]);

        return Response::success($item , 'تغییر وضعیت با موفقیت انجام شد.');
    }
}
