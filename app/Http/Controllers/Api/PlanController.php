<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Carbon\Carbon;
use App\Models\Pay;

class PlanController extends Controller
{
    public function list(Request $request)
    {
        $data = Plan::orderBy('id' , 'DESC')->get();

        return Response::success($data , 'لیست پلن ها با موفقیت دریافت شد .');
    }
    
    public function buy_plan(Request $request)
    {
        $validation = $this->validateData($request , [
            'id' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);
        $today = Carbon::today()->format('Y-m-d');

        $plan = Plan::find($request->id);
        if(!$plan)
        {
            return Response::error(null , 'درخواست نامعتبر .' , null);
        }
        $token = md5(uniqid(microtime(), true));
        $description = 'درخواست ارتقای پلن به '.$plan->title.' از طریق درگاه';
        $pay = Pay::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'token' => $token,
            'description' => $description,
            'expire' => Carbon::now()->addMinutes(30)
        ]);

        $callback = route('pay.buy_plan' , ['token'=>$pay->token]);
        
        $data = array(
            "merchant_id" => env('MERCHENT_ID'),
            "amount" => $pay->amount*10,
            "callback_url" => $callback,
            "description" => "خرید پلن",
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
