<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Harvest;
use Carbon\Carbon;

class HarvestController extends Controller
{
    public function add_harvest(Request $request)
    {
        $user = $this->getUserByToken($request);
    
        $validation = $this->validateData($request , [
            'card_number' => 'required',
            'amount' => 'required',
        ]);
        if($validation){
            return $validation;
        }

        $set = $this->settings();

        if($request->amount < $set->minimum_harvest_amount){
            return Response::error(null , 'حداقل مقدار برای ثبت برداشت '.$set->minimum_harvest_amount.'تومان است.' , null);
        }
        if($request->amount > $user->cash){
            return Response::error(null , 'موجودی شما کافی نمیباشد.' , null);
        }

        $harvest = $user->harvests()->create([
            'card_number' => $request->card_number,
            'shaba_number' => $request->shaba_number,
            'amount' => $request->amount,
        ]);

        $user->update([
            'cash' => $user->cash -= $harvest->amount
        ]);

        return Response::success($harvest , 'ثبت درخواست انجام شد.منتظر تایید ادمین باشید.');
    }

    public function harvest_list(Request $request)
    {
        $user = $this->getUserByToken($request);

        $list = $user->harvests()->latest()->paginate(25);

        foreach($list as $item){
            $item['create'] = Jalalian::forge($item->created_at)->format('%Y/%m/%d-H:i');
        }

        return Response::success($list , 'لیست برداشت ها با موفقیت دریافت شد .');
    }

    public function all_harvests(Request $request)
    {
        $date = Carbon::today()->subDays(20);
        $list = Harvest::where('status' , 1)
        ->where('updated_at' , '>' , $date)
        ->with('user')->paginate(50);
        foreach($list as $item){
            $item['create'] = Jalalian::forge($item->created_at)->format('%Y/%m/%d');
        }
        return Response::success($list , 'لیست برداشت های قبلی با موفقیت دریافت شد .');
    }
    
}
