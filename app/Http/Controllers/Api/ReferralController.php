<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\UserReferral;
use App\Models\User;
use App\Models\Pay;
use App\Models\View;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class ReferralController extends Controller
{
    public function get_referral(Request $request)
    {
        $validation = $this->validateData($request , [
            'count' => 'required|numeric'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);
        $settings = $this->settings();
        $count = $request->count;
        $user_plan = $user->user_plan;
        $plan = $user_plan->plan;
        $today = Carbon::today();

        if($count < 1)
        {
            return Response::error(null , 'درخواست نامعتبر..' , null);
        }

        if($user_plan->can_rent_referral_at && $user_plan->can_rent_referral_at > $today)
        {
            return Response::error(null , 'محدودیت اجاره زیرمجموعه برای شما به اتمام نرسیده است.' , null);
        }
        
        $count_referrals = $user->user_referrals()->where('expires_at' , '>' , $today)->count();
        if($count_referrals+$count > $plan->max_referral){
            return Response::error(null , 'تعداد زیرمجموعه انتخاب شده بیشتر از حد مجاز شده است .' , null);
        }
        
        $required_coins = $count * $plan->referral_cost_coin;
        if($required_coins > $user->coins){
            return Response::error(null , 'به تعداد کافی سکه ندارید .' , null);
        }

        $referrals = $this->findUsersAsReferral($user , $count);

        if(count($referrals) < $count)
        {
            return Response::error(null , 'در حال حاضر درخواست شما مقدور نمی باشد.' , null);
        }
        
        $user->update([
            'coins'=>$user->coins-=$required_coins
        ]);

        $one_month_later = Carbon::today()->addDays(30);
        foreach($referrals as $item){
            $user->user_referrals()->create([
                'referral_id'=>$item['id'] ,
                'expires_at'=>$one_month_later
            ]);
        }
        
        $user_plan->update([
            'can_rent_referral_at'=>$today->addDays($plan->subset_rent_time)->format('Y-m-d')
        ]);
        
        return Response::success(null , 'زیرمجموعه ها با موفقیت اضافه شدند .');
    }

    public function referrals(Request $request)
    {
        $user = $this->getUserByToken($request);

        $today = Carbon::today();

        $data = $user->user_referrals()->where('expires_at' , '>' , $today)->get();

        if($data){
            foreach($data as $item){
                $ref = User::find($item->referral_id);
                if($ref){
                    $diff = $ref->created_at->diffInDays($today);
                    $item->referral = $ref->fullname;
                    $item->all_views = floor(View::where('user_id' , $ref->id)->where('type' , 'video')->count() / ($diff + 1));
                }else{
                    $item->all_views = 0;
                    $item->referral = null;
                }
                $item['create'] = Jalalian::forge($item->created_at)->format('%Y/%m/%d');
                $item['expire'] = Jalalian::forge($item->expires_at)->format('%Y/%m/%d');
            }
        }

        return Response::success($data , 'لیست زیرمجموعه ها با موفقیت دریافت شد .');
    }
    
    public function update_referral(Request $request)
    {
        $validation = $this->validateData($request , [
            'referrals' => 'required|array',
            'type' => 'required',
        ]);
        if($validation){
            return $validation;
        }
        if($request->type == 2){
            $type = 2;
        }else{
            $type = 1;
        }

        $user = $this->getUserByToken($request);
        $settings = $this->settings();
        $user_plan = $user->user_plan;
        $plan = $user_plan->plan;
        $today = Carbon::today();

        // if($user_plan->can_rent_referral_at && $user_plan->can_rent_referral_at > $today)
        // {
        //     return Response::error(null , 'محدودیت اجاره زیرمجموعه برای شما به اتمام نرسیده است.' , null);
        // }

        $required_coins = 0;
        $required_cash = 0;
        $suc = 0;
        $err = 0;
        $one_month_later = Carbon::today()->addDays(30);
        
        foreach($request->referrals as $item){

            $ref = UserReferral::where('id' , $item)->where('user_id' , $user->id)->first();
            if($ref){

                if($type == 1){
                    $required_coins += $plan->referral_cost_coin;
                }else{
                    $required_cash += $plan->referral_cost_cash;
                }

                $suc++;
            }else{
                $err++;
            }

        }

        if($type == 1){
            $required_coins = round(($required_coins * $plan->discount) / 100);

            if($user->coins < $required_coins){
                return Response::error(null , 'به تعداد کافی سکه ندارید.' , null);
            }
            $user->update([
                'coins'=>$user->coins - $required_coins
            ]);
        }else{
            $required_cash = round(($required_cash * $plan->discount) / 100);
            
            if($user->cash < $required_cash){
                return Response::error(null , 'به تعداد کافی موجودی ندارید.' , null);
            }
            $user->update([
                'cash'=>$user->cash - $required_cash
            ]);
        }

        foreach($request->referrals as $item){
            $ref = UserReferral::where('id' , $item)->where('user_id' , $user->id)->first();
            if($ref){
                $new_date = Carbon::createFromFormat('Y-m-d', $ref->expires_at)->addDays(30);
                $ref->update([
                    'expires_at'=>$new_date
                ]);
            }
        }

        $user_plan->update([
            'can_rent_referral_at'=>$today->addDays($plan->subset_rent_time)->format('Y-m-d')
        ]);
        
        return Response::success(null ,$suc . ' زیرمجموعه با موفقیت تمدید شد.');
    }
    
    public function pay_referral(Request $request)
    {
        $validation = $this->validateData($request , [
            'count' => 'required|numeric'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);
        $settings = $this->settings();
        $count = $request->count;
        $user_plan = $user->user_plan;
        $plan = $user_plan->plan;
        $today = Carbon::today()->format('Y-m-d');

        if($count < 1)
        {
            return Response::error(null , 'درخواست نامعتبر..' , null);
        }

        if($user_plan->can_rent_referral_at && $user_plan->can_rent_referral_at > $today)
        {
            return Response::error(null , 'محدودیت اجاره زیرمجموعه برای شما به اتمام نرسیده است.' , null);
        }
        
        $count_referrals = $user->user_referrals()->where('expires_at' , '>' , $today)->count();
        if($count_referrals+$count > $plan->max_referral){
            return Response::error(null , 'تعداد زیرمجموعه انتخاب شده بیشتر از حد مجاز شده است .' , null);
        }

        if($count_referrals+$count > $plan->max_referral){
            return Response::error(null , 'تعداد زیرمجموعه انتخاب شده بیشتر از حد مجاز شده است .' , null);
        }

        $referrals = $this->findUsersAsReferral($user , $count);

        if(count($referrals) < $count)
        {
            return Response::error(null , 'در حال حاضر درخواست شما مقدور نمی باشد.' , null);
        }
        
        $required_cash = $count * $plan->referral_cost_cash;
        $token = md5(uniqid(microtime(), true));
        $description = 'درخواست خرید '.$count.' زیر مجموعه از طریق درگاه';
        $pay = Pay::create([
            'user_id' => $user->id,
            'amount' => $required_cash,
            'token' => $token,
            'description' => $description,
            'expire' => Carbon::now()->addMinutes(30)
        ]);

        $callback = route('pay.referral' , ['token'=>$pay->token]);
        
        $data = array(
            "merchant_id" => env('MERCHENT_ID'),
            "amount" => $required_cash*10,
            "callback_url" => $callback,
            "description" => "خرید رفرال از طریق درگاه بانکی",
            "metadata" => [ "email" => $user->email],
        );
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true, JSON_PRETTY_PRINT);
        curl_close($ch);

        if ($err) {
            $pay->update([
                'status'=>2,
                'pay_description'=>'خطا در ثبت تراکنش در درگاه.',
            ]);
            return Response::error(null , 'خطای اتصال به درگاه رخ داده است .' , null);
        } else {
            if (empty($result['errors'])) {
                if ($result['data']['code'] == 100) {
                    $link = 'https://www.zarinpal.com/pg/StartPay/' . $result['data']["authority"];
                    return Response::success($link , 'انتقال به درگاه ...');
                }
            } else {
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'خطا در ثبت تراکنش در درگاه.',
                ]);
                // echo'Error Code: ' . $result['errors']['code'];
                // echo'message: ' .  $result['errors']['message'];
                return Response::error(null , "خطای اتصال به درگاه (".$result['errors']['message'].") ." , null);
            }
        }
        
    }
}
