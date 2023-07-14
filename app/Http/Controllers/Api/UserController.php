<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Models\Plan;
use App\Models\User;
use App\Models\Post;
use App\Models\Pay;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class UserController extends Controller
{
    public function get_user(Request $request)
    {
        $user = $this->getUserByToken($request);
        $today = Carbon::today();
        $user->bank_account;
        $user_plan = $user->user_plan;
        $user->plan = Plan::find($user->user_plan->plan_id);

        if($user_plan->expiry && $user_plan->expiry < $today){
            $user_plan->update([
                'plan_id'=>1,
                'expiry'=>null,
            ]);
        }

        $user->referrals_count = $user->user_referrals()->where('expires_at' , '>' , $today)->count();
        if($user->user_plan->can_rent_referral_at && $user->user_plan->can_rent_referral_at > $today){
            $user->referral_time = $today->diffInDays($user->user_plan->can_rent_referral_at);
        }else{
            $user->referral_time = 0;
        }

        $last_harvest = $user->harvests()->latest()->where('status' , 1)->first();
        if($last_harvest && $last_harvest->created_at < $today){
            $diff = $today->diffInDays($last_harvest->created_at);
            $user->harvest_time = $user->plan->harvest_time - $diff;
        }else{
            $user->harvest_time = $user->plan->harvest_time;
        }

        if($user->user_plan->expiry && $user->user_plan->expiry > $today){
            $user->plan_expiry = $today->diffInDays($user->user_plan->expiry);
        }else{
            $user->plan_expiry = 'نامحدود';
        }

        $last_message_seen = $user->user_plan->last_message_seen;
        if($last_message_seen){
            $user->unread_messages = Post::where('type',4)->where('status',1)->where('created_at','>',$last_message_seen)->count();
        }else{
            $user->unread_messages = Post::where('type',4)->where('status',1)->count();
        }

        return Response::success($user , 'اطلاعات کاربر با موفقیت دریافت شد .');
    }

    public function change_credit(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'type' => 'required',
            'credit' => 'required',
            'amount' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $t = $request->type;
        $c = $request->credit;
        $a = $request->amount;

        $cash = $user->cash;
        $coin = $user->coin;

        // type 1 == زیاد کردن موجودی
        // type 2 == کم کردن موجودی
        if($t == 1)
        {
            if($c == 'coin')
            {
                $user->update(['coin' => $coin+$a]);
            }
            elseif($c == 'cash')
            {
                $user->update(['cash' => $cash+$a]);

                $user->user_plan->update([
                    'income' => $user->user_plan->income += $a
                ]);
            }
        }
        elseif($t == 2)
        {
            if($c == 'coin')
            {
                if($coin >= $a){
                    $user->update(['coin' => $coin-$a]);
                }else{
                    return Response::error(null , 'به اندازه کافی موجودی ندارید .' , null);
                }
            }
            elseif($c == 'cash')
            {
                if($cash >= $a){
                    $user->update(['cash' => $cash-$a]);
                }else{
                    return Response::error(null , 'به تعداد کافی سکه ندارید .' , null);
                }
            }
        }

        $user = $this->getUserByToken($request);

        return Response::success($user , 'عملیات با موفقیت انجام شد .');
    }

    public function convert(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'amount' => 'required|numeric'
        ]);
        if($validation){
            return $validation;
        }

        $amount = $request->amount;
        if($amount < 1000)
        {
            return Response::error(null , 'حداقل 1000 تومان باید وارد کنید.' , null);
        }

        if($amount > $user->cash)
        {
            return Response::error(null , 'موجودی کافی ندارید .' , null);
        }

        $coins = round(($amount / 100) * 16); // هر 100 تومان معادل 16 سکه
        $recieved_coins = ($coins * 0.2) + $coins; // بیست درصد تخفیف بیشتر

        $user->update([
            'cash' => $user->cash -= $amount,
            'coins' => $user->coins += $recieved_coins
        ]);

        $user = $this->getUserByToken($request);

        return Response::success($user , 'عملیات با موفقیت انجام شد .');
    }

    public function saveFcmRefreshToken(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'token' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $user->update([
            'fcm_refresh_token' => $request->token
        ]);

        $user = $this->getUserByToken($request);

        return Response::success($user , 'عملیات با موفقیت انجام شد .');
    }

    public function add_credit(Request $request)
    {
        $validation = $this->validateData($request , [
            'amount' => 'required|numeric|min:10000'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);
        $today = Carbon::today()->format('Y-m-d');

        $token = md5(uniqid(microtime(), true));
        $description = 'افزایش اعتبار از سمت فروشگاه';
        $pay = Pay::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'token' => $token,
            'description' => $description,
            'expire' => Carbon::now()->addMinutes(30)
        ]);

        $callback = route('pay.add_credit' , ['token'=>$pay->token]);

        $data = array(
            "merchant_id" => 'b90349e6-9dd2-4e5e-a3e7-6477f81ab6e4',
            "amount" => $pay->amount*10,
            "callback_url" => $callback,
            "description" => 'افزایش اعتبار از سمت فروشگاه',
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
                return Response::error(null , "خطای اتصال به درگاه (".$result['errors']['message'].") ." , null);
            }
        }

    }

    public function sellerRegister(Request $request)
    {
        $user = $this->getUserByToken($request);

        $user->update([
            'is_seller' => 1
        ]);

        return Response::success(null , null);
    }

    public function get_bank_account(Request $request)
    {
        $user = $this->getUserByToken($request);
        $bank = $user->bank_account;
        if(!$bank){
            $bank = [];
        }
        $bank['new_card_image'] = null;
        return Response::success($bank , null);
    }

    public function edit_bank_account(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'card_number' => 'required',
            'shaba_number' => 'required',
            'new_card_image' => 'image'
        ]);
        if($validation){
            return $validation;
        }

        $bank = $user->bank_account;
        if($bank)
        {
            $bank->update([
                'card_number' => $request->card_number,
                'shaba_number' => $request->shaba_number,
                'status' => 0
            ]);
        }else{
            $bank = $user->bank_account()->create([
                'card_number' => $request->card_number,
                'shaba_number' => $request->shaba_number,
                'card_image' => ''
            ]);
        }

        if($request->hasFile('new_card_image')){
            if($bank->card_image){
                // File::delete(public_path().'/uploads/'.$bank->card_image);
            }
            $bank->update([
                'card_image' => $this->uploadFile($request->new_card_image , 'users/'.$user->id),
            ]);
        }

        $user = $this->getUserByToken($request);

        return Response::success($bank , 'اطلاعات حساب با موفقیت بروزرسانی شد .');
    }

}
