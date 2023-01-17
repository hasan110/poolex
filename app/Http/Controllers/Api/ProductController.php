<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Pay;

class ProductController extends Controller
{
    public function list(Request $request)
    {
        $data = Product::orderBy('priority' , 'ASC')->get();

        return Response::success($data , 'لیست محصولات با موفقیت دریافت شد .');
    }
    
    public function buy_product(Request $request)
    {
        $validation = $this->validateData($request , [
            'id' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);
        $today = Carbon::today()->format('Y-m-d');

        $product = Product::find($request->id);
        if(!$product)
        {
            return Response::error(null , 'درخواست نامعتبر .' , null);
        }
        $token = md5(uniqid(microtime(), true));
        $description = 'درخواست خرید محصول از فروشگاه از طریق درگاه';
        $pay = Pay::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'amount' => $product->price,
            'token' => $token,
            'description' => $description,
            'expire' => Carbon::now()->addMinutes(30)
        ]);

        $callback = route('pay.buy_product' , ['token'=>$pay->token]);
        
        $data = array(
            "merchant_id" => env('MERCHENT_ID'),
            "amount" => $pay->amount*10,
            "callback_url" => $callback,
            "description" => "خرید سکه از فروشگاه",
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
            return Response::error(null , 'خطای اتصال به درگاه رخ داده است .' , null);
        } else {
            if (empty($result['errors'])) {
                if ($result['data']['code'] == 100) {
                    $link = 'https://www.zarinpal.com/pg/StartPay/' . $result['data']["authority"];
                    return Response::success($link , 'انتقال به درگاه ...');
                }
            } else {
                // echo'Error Code: ' . $result['errors']['code'];
                // echo'message: ' .  $result['errors']['message'];
                return Response::error(null , "خطای اتصال به درگاه (".$result['errors']['message'].") ." , null);
            }
        }
        
    }
    
    public function buy_offer(Request $request)
    {
        $user = $this->getUserByToken($request);
        $user_plan = $user->user_plan;
        $plan = $user_plan->plan;

        if($user_plan->can_use_offer_at >= Carbon::today()){
            return Response::error(null , 'استفاده از آفر در روز تنها یک بار مجاز است.' , null);
        }

        $token = md5(uniqid(microtime(), true));
        $description = 'درخواست خرید آفر فروشگاه از طریق درگاه';
        $pay = Pay::create([
            'user_id' => $user->id,
            'amount' => $plan->offer_cost,
            'token' => $token,
            'description' => $description,
            'expire' => Carbon::now()->addMinutes(30)
        ]);

        $callback = route('pay.buy_offer' , ['token'=>$pay->token]);
        
        $data = array(
            "merchant_id" => env('MERCHENT_ID'),
            "amount" => $pay->amount*10,
            "callback_url" => $callback,
            "description" => "خرید سکه از فروشگاه",
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
