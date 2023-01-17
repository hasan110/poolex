<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Award;
use App\Models\Pay;
use App\Models\UserAward;
use Carbon\Carbon;

class AwardController extends Controller
{
    public function list(Request $request)
    {
        $data = Award::where('status' , 1)->get();

        return Response::success($data , 'لیست جوایز با موفقیت دریافت شد .');
    }

    public function get_random_award(Request $request)
    {
        $user = $this->getUserByToken($request);
        
        $type = $request->type;
        $set = $this->settings();

        if($type == 'coins')
        {
            if($user->coins < $set->award_coin_reduction){
                return Response::error(null , 'به اندازه کافی سکه ندارید.' , null);
            }

        }elseif($type == 'cash')
        {
            if($user->cash < $set->award_cash_reduction){
                return Response::error(null , 'به اندازه کافی موجودی ندارید.' , null);
            }
            
        }else{
            return Response::error(null , 'درخواست نامعتبر.' , null);
        }

        $award = Award::where('status' , 1)->where('count' , '>' , 0)->inRandomOrder()->first();

        if(!$award){
            return Response::error(null , 'بعدا تلاش کنید.' , null);
        }
        
        if($type == 'coins'){
            $user->update([
                'coins'=>$user->coins -= $set->award_coin_reduction
            ]);
        }
        if($type == 'cash'){
            $user->update([
                'cash'=>$user->cash -= $set->award_cash_reduction
            ]);
        }

        if($award->type == 1){
            $user->coins = $user->coins += $award->amount;
            $user->save();
        }
        if($award->type == 2){
            $user->cash = $user->cash += $award->amount;
            $user->save();
                
            $user->user_plan->update([
                'income' => $user->user_plan->income += $award->amount
            ]);
        }

        if($award->type == 1 || $award->type == 2){
            $r = 0;
            $s = 1;
        }else{
            $r = 1;
            $s = 0;
        }
        $user_award = $user->user_awards()->create([
            'award_id' => $award->id,
            'status'=>$s,
        ]);

        $award->update([
            'count'=>$award->count -= 1
        ]);

        $res = [
            'number_required' => $r,
            'user_award' => $user_award,
            'award' => $award,
        ];
        
        return Response::success($res , 'یک جایزه بصورت تصادفی انتخاب شد .');
    }

    public function confirm_award(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'id' => 'required',
            'number' => 'required',
        ]);
        if($validation){
            return $validation;
        }

        $user_award = UserAward::where('id' , $request->id)->where('user_id' , $user->id)->where('status' , 0)->first();

        if(!$user_award){
            return Response::error(null , 'درخواست نامعتبر' , null);
        }
        
        $user_award->update([
            'number' => $request->number
        ]);
        
        return Response::success($user_award , 'درخواست با موفقیت ثبت شد.');
    }
    
    public function pay_award(Request $request)
    {
        $validation = $this->validateData($request , [
            'count' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);
        $set = $this->settings();
        $amount = $set->online_pay_award_cost * $request->count;

        $token = md5(uniqid(microtime(), true));
        $description = 'درخواست خرید  '.$request->count.' شانس از طریق درگاه';
        $pay = Pay::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'token' => $token,
            'description' => $description,
            'expire' => Carbon::now()->addMinutes(30)
        ]);

        $callback = route('pay.buy_award' , ['token'=>$pay->token]);
        
        $data = array(
            "merchant_id" => env('MERCHENT_ID'),
            "amount" => $pay->amount*10,
            "callback_url" => $callback,
            "description" => "خرید جعبه شانس از درگاه",
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

    public function pay_random_award(Request $request)
    {
        $user = $this->getUserByToken($request);
        $set = $this->settings();
        $user_plan = $user->user_plan;

        if($user_plan->award_paid_count < 1){
            return Response::error(null , 'نمیتوانید جایزه شانسی انتخاب کنید.' , null);
        }

        $award = Award::where('status' , 1)->where('count' , '>' , 0)->inRandomOrder()->first();

        if(!$award){
            return Response::error(null , 'بعدا تلاش کنید.' , null);
        }

        $user->user_plan->update([
            'award_paid_count' => $user->user_plan->award_paid_count -= 1
        ]);

        if($award->type == 1){
            $user->coins = $user->coins += $award->amount;
            $user->save();
        }
        if($award->type == 2){
            $user->cash = $user->cash += $award->amount;
            $user->save();
                
            $user->user_plan->update([
                'income' => $user->user_plan->income += $award->amount
            ]);
        }

        if($award->type == 1 || $award->type == 2){
            $r = 0;
            $s = 1;
        }else{
            $r = 1;
            $s = 0;
        }
        $user_award = $user->user_awards()->create([
            'award_id' => $award->id,
            'status'=>$s,
        ]);

        $award->update([
            'count'=>$award->count -= 1
        ]);

        $res = [
            'number_required' => $r,
            'user_award' => $user_award,
            'award' => $award,
        ];
        
        return Response::success($res , 'یک جایزه بصورت تصادفی انتخاب شد .');
    }
}
