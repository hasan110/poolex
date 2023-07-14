<?php

namespace App\Http\Controllers\Admin;

use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Pay;
use App\Models\Invoice;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class PayController extends Controller
{
    public function pay_referral(Request $request)
    {
        $pay=Pay::where('token',$request->token)
        ->where('status',0)
        ->latest()
        ->first();

        $data = $request->all();

        if(!$pay)
        {
            return view('pay.payment' , ['data'=> $data , 'msg'=>'پرداخت ناموفق بود . لطفا دوباره تلاش کنید .' , 'status'=>0]);
        }

        $time = Jalalian::forge($pay->created_at)->format('H:i:s - %Y/%m/%d');

        if($request->Status == "NOK"){
            $pay->update([
                'status'=>2,
                'pay_description'=>'پرداخت ناموفق بود.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'پرداخت ناموفق بود .' , 'status'=>0]);
        }

        $data = array("merchant_id" => env('MERCHENT_ID'), "authority" => $request->Authority, "amount" => $pay->amount*10);
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($err) {
            $pay->update([
                'status'=>2,
                'pay_description'=>'خطا در ارسال درخواست تایید تراکنش.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش .' , 'status'=>0]);
        } else {
            if ($result['data']['code'] == 100) {

                $user = User::find($pay->user_id);
                $user_plan = $user->user_plan;
                $plan = $user_plan->plan;
                $today = Carbon::today();

                $count = $pay->amount / $plan->referral_cost_cash;

                $referrals = $this->findUsersAsReferral($user , $count);

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

                $pay->update([
                    'status'=>1,
                    'pay_description'=>'پرداخت موفق بود. زیر مجموعه ها با موفقیت اضافه شدند.',
                ]);

                return view('pay.payment' , ['transaction_id'=> $result['data']['ref_id'] , 'time'=> $time , 'data'=> $data , 'msg'=>'تراکنش با موفقیت انجام شد.' , 'status'=>1]);
            }elseif($result['data']['code'] == 101){

                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت به دلیل تکراری بودن تراکنش ناموفق بود.',
                ]);

                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'تراکنش تکراری است.' , 'status'=>0]);
            } else {

                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت هنگام تکمیل تراکنش با مشکل مواجه شد.',
                ]);

                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش ' , 'status'=>0]);
            }
        }
    }

    public function buy_plan(Request $request)
    {
        $pay=Pay::where('token',$request->token)
        ->where('status',0)
        ->latest()
        ->first();

        $data = $request->all();

        if(!$pay)
        {
            return view('pay.payment' , ['data'=> $data , 'msg'=>'پرداخت ناموفق بود . لطفا دوباره تلاش کنید .' , 'status'=>0]);
        }

        $time = Jalalian::forge($pay->created_at)->format('H:i:s - %Y/%m/%d');

        if($request->Status == "NOK"){
            $pay->update([
                'status'=>2,
                'pay_description'=>'پرداخت ناموفق بود.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'پرداخت ناموفق بود .' , 'status'=>0]);
        }

        $data = array("merchant_id" => env('MERCHENT_ID'), "authority" => $request->Authority, "amount" => $pay->amount*10);
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($err) {
            $pay->update([
                'status'=>2,
                'pay_description'=>'خطا در ارسال درخواست تایید تراکنش.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش .' , 'status'=>0]);
        } else {
            if ($result['data']['code'] == 100) {

                $user = User::find($pay->user_id);
                $user_plan = $user->user_plan;
                $plan = Plan::find($pay->plan_id);
                $today = Carbon::today();

                $user_plan->update([
                    'plan_id' => $plan->id,
                    'expiry' => Carbon::today()->addMonths($plan->validity)
                ]);

                $pay->update([
                    'status'=>1,
                    'pay_description'=>'درخواست خرید '.$plan->title.' با موفقیت تکمیل شد.',
                ]);

                return view('pay.payment' , ['transaction_id'=> $result['data']['ref_id'] , 'time'=> $time , 'data'=> $data , 'msg'=>'خریداری پلن با موفقیت انجام شد.' , 'status'=>1]);
            }elseif($result['data']['code'] == 101){
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت به دلیل تکراری بودن تراکنش ناموفق بود',
                ]);
                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'تراکنش تکراری است.' , 'status'=>0]);
            } else {
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت هنگام تکمیل تراکنش با مشکل مواجه شد.',
                ]);
                // echo'code: ' . $result['errors']['code'];
                // echo'message: ' .  $result['errors']['message'];
                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش ' , 'status'=>0]);
            }
        }
    }

    public function buy_product(Request $request)
    {
        $pay=Pay::where('token',$request->token)
        ->where('status',0)
        ->latest()
        ->first();

        $data = $request->all();

        if(!$pay)
        {
            return view('pay.payment' , ['data'=> $data , 'msg'=>'پرداخت ناموفق بود . لطفا دوباره تلاش کنید .' , 'status'=>0]);
        }

        $time = Jalalian::forge($pay->created_at)->format('H:i:s - %Y/%m/%d');

        if($request->Status == "NOK"){
            $pay->update([
                'status'=>2,
                'pay_description'=>'پرداخت ناموفق بود.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'پرداخت ناموفق بود .' , 'status'=>0]);
        }

        $data = array("merchant_id" => env('MERCHENT_ID'), "authority" => $request->Authority, "amount" => $pay->amount*10);
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($err) {
            $pay->update([
                'status'=>2,
                'pay_description'=>'خطا در ارسال درخواست تایید تراکنش.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش .' , 'status'=>0]);
        } else {
            if ($result['data']['code'] == 100) {

                $user = User::find($pay->user_id);
                $today = Carbon::today();

                $product = Product::find($pay->product_id);

                $user->update([
                    'coins' => $user->coins + $product->coin
                ]);

                $pay->update([
                    'status'=>1,
                    'pay_description'=>'محصول مورد نظر با موفقیت خریداری شد.',
                ]);

                return view('pay.payment' , ['transaction_id'=> $result['data']['ref_id'] , 'time'=> $time , 'data'=> $data , 'msg'=>'خرید سکه با موفقیت انجام شد.' , 'status'=>1]);
            }elseif($result['data']['code'] == 101){
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت به دلیل تکراری بودن تراکنش ناموفق بود',
                ]);
                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'تراکنش تکراری است.' , 'status'=>0]);
            } else {
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت هنگام تکمیل تراکنش با مشکل مواجه شد.',
                ]);
                // echo'code: ' . $result['errors']['code'];
                // echo'message: ' .  $result['errors']['message'];
                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش ' , 'status'=>0]);
            }
        }
    }

    public function buy_offer(Request $request)
    {
        $pay=Pay::where('token',$request->token)
        ->where('status',0)
        ->latest()
        ->first();

        $data = $request->all();

        if(!$pay)
        {
            return view('pay.payment' , ['data'=> $data , 'msg'=>'پرداخت ناموفق بود . لطفا دوباره تلاش کنید .' , 'status'=>0]);
        }

        $time = Jalalian::forge($pay->created_at)->format('H:i:s - %Y/%m/%d');

        if($request->Status == "NOK"){
            $pay->update([
                'status'=>2,
                'pay_description'=>'پرداخت ناموفق بود.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'پرداخت ناموفق بود .' , 'status'=>0]);
        }

        $data = array("merchant_id" => env('MERCHENT_ID'), "authority" => $request->Authority, "amount" => $pay->amount*10);
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($err) {
            $pay->update([
                'status'=>2,
                'pay_description'=>'خطا در ارسال درخواست تایید تراکنش.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش .' , 'status'=>0]);
        } else {
            if ($result['data']['code'] == 100) {

                $user = User::find($pay->user_id);
                $user_plan = $user->user_plan;
                $plan = $user_plan->plan;

                $user->update([
                    'coins' => $user->coins + $plan->offer_coin
                ]);

                $user_plan->update([
                    'can_use_offer_at' => Carbon::today()->addDay()
                ]);

                $pay->update([
                    'status'=>1,
                    'pay_description'=>'آفر فروشگاه با موفقیت خریداری شد.',
                ]);

                return view('pay.payment' , ['transaction_id'=> $result['data']['ref_id'] , 'time'=> $time , 'data'=> $data , 'msg'=>'خرید سکه با موفقیت انجام شد.' , 'status'=>1]);
            }elseif($result['data']['code'] == 101){
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت به دلیل تکراری بودن تراکنش ناموفق بود',
                ]);
                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'تراکنش تکراری است.' , 'status'=>0]);
            } else {
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت هنگام تکمیل تراکنش با مشکل مواجه شد.',
                ]);
                // echo'code: ' . $result['errors']['code'];
                // echo'message: ' .  $result['errors']['message'];
                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش ' , 'status'=>0]);
            }
        }
    }

    public function buy_award(Request $request)
    {
        $pay=Pay::where('token',$request->token)
        ->where('status',0)
        ->latest()
        ->first();

        $data = $request->all();

        if(!$pay)
        {
            return view('pay.payment' , ['data'=> $data , 'msg'=>'پرداخت ناموفق بود . لطفا دوباره تلاش کنید .' , 'status'=>0]);
        }

        $time = Jalalian::forge($pay->created_at)->format('H:i:s - %Y/%m/%d');

        if($request->Status == "NOK"){
            $pay->update([
                'status'=>2,
                'pay_description'=>'پرداخت ناموفق بود.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'پرداخت ناموفق بود .' , 'status'=>0]);
        }

        $data = array("merchant_id" => env('MERCHENT_ID'), "authority" => $request->Authority, "amount" => $pay->amount*10);
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($err) {
            $pay->update([
                'status'=>2,
                'pay_description'=>'خطا در ارسال درخواست تایید تراکنش.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش .' , 'status'=>0]);
        } else {
            if ($result['data']['code'] == 100) {

                $user = User::find($pay->user_id);
                $set = $this->settings();
                $user_plan = $user->user_plan;

                $award_paid_count = round(($pay->amount) / $set->online_pay_award_cost);
                $user->user_plan->update([
                    'award_paid_count' => $user->user_plan->award_paid_count + $award_paid_count
                ]);

                $pay->update([
                    'status'=>1,
                    'pay_description'=>'به تعداد '.$award_paid_count.' جایزه با موفقیت خریداری شد.',
                ]);

                return view('pay.payment' , ['transaction_id'=> $result['data']['ref_id'] , 'time'=> $time , 'data'=> $data , 'msg'=>'خرید جعبه شانس با موفقیت انجام شد.' , 'status'=>1]);
            }elseif($result['data']['code'] == 101){
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت به دلیل تکراری بودن تراکنش ناموفق بود',
                ]);
                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'تراکنش تکراری است.' , 'status'=>0]);
            } else {
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت هنگام تکمیل تراکنش با مشکل مواجه شد.',
                ]);
                // echo'code: ' . $result['errors']['code'];
                // echo'message: ' .  $result['errors']['message'];
                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش ' , 'status'=>0]);
            }
        }
    }

    public function add_credit(Request $request)
    {
        $pay=Pay::where('token',$request->token)
        ->where('status',0)
        ->latest()
        ->first();

        $data = $request->all();

        if(!$pay)
        {
            return view('pay.payment' , ['data'=> $data , 'msg'=>'پرداخت ناموفق بود . لطفا دوباره تلاش کنید .' , 'status'=>0]);
        }

        $time = Jalalian::forge($pay->created_at)->format('H:i:s - %Y/%m/%d');

        if($request->Status == "NOK"){
            $pay->update([
                'status'=>2,
                'pay_description'=>'پرداخت ناموفق بود.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'پرداخت ناموفق بود .' , 'status'=>0]);
        }

        $data = array("merchant_id" => env('MERCHENT_ID'), "authority" => $request->Authority, "amount" => $pay->amount*10);
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($err) {
            $pay->update([
                'status'=>2,
                'pay_description'=>'خطا در ارسال درخواست تایید تراکنش.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش .' , 'status'=>0]);
        } else {
            if ($result['data']['code'] == 100) {

                $user = User::find($pay->user_id);
                $today = Carbon::today();

                $user->update([
                    'cash' => $user->cash + $pay->amount
                ]);

                $pay->update([
                    'status'=>1,
                    'pay_description'=>'افزایش اعتبار با موفقیت انجام شد.',
                ]);

                return view('pay.payment' , ['transaction_id'=> $result['data']['ref_id'] , 'time'=> $time , 'data'=> $data , 'msg'=>'خرید سکه با موفقیت انجام شد.' , 'status'=>1]);
            }elseif($result['data']['code'] == 101){
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت به دلیل تکراری بودن تراکنش ناموفق بود',
                ]);

                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'تراکنش تکراری است.' , 'status'=>0]);
            } else {
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت هنگام تکمیل تراکنش با مشکل مواجه شد.',
                ]);

                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش ' , 'status'=>0]);
            }
        }
    }

    public function pay_order(Request $request)
    {
        $pay=Pay::where('token',$request->token)
        ->where('status',0)
        ->latest()
        ->first();

        $data = $request->all();

        if(!$pay)
        {
            return view('pay.payment' , ['data'=> $data , 'msg'=>'پرداخت ناموفق بود . لطفا دوباره تلاش کنید .' , 'status'=>0]);
        }

        $time = Jalalian::forge($pay->created_at)->format('H:i:s - %Y/%m/%d');

        if($request->Status == "NOK"){
            $pay->update([
                'status'=>2,
                'pay_description'=>'پرداخت ناموفق بود.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'پرداخت ناموفق بود .' , 'status'=>0]);
        }

        $data = array("merchant_id" => env('MERCHENT_ID'), "authority" => $request->Authority, "amount" => $pay->amount*10);
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($err) {
            $pay->update([
                'status'=>2,
                'pay_description'=>'خطا در ارسال درخواست تایید تراکنش.',
            ]);
            return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش .' , 'status'=>0]);
        } else {
            if ($result['data']['code'] == 100) {

                $user = User::find($pay->user_id);
                $today = Carbon::today();

                $invoice = Invoice::where([
                    'id'=>$pay->invoice_id,
                    'status'=>1,
                    'user_id'=>$user->id,
                ])->first();

                if(!$invoice){
                    return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'سفارش مورد نظر یافت نشد .' , 'status'=>0]);
                }

                $invoice->update([
                    'status'=>2
                ]);

                foreach ($invoice->items as $detail)
                {
                    $product = StoreProduct::find($detail['store_product_id']);
                    if($product)
                    {
                        $new_inventory = $product->inventory - $detail['count'];
                        if($new_inventory < 1){
                            $new_inventory = 0;
                        }
                        $product->update([
                            'inventory'=>$new_inventory
                        ]);
                    }
                }

                $pay->update([
                    'status'=>1,
                    'pay_description'=>'پرداخت هزینه سفارش با موفقیت انجام شد.',
                ]);

                return view('pay.payment' , ['transaction_id'=> $result['data']['ref_id'] , 'time'=> $time , 'data'=> $data , 'msg'=>'خرید سکه با موفقیت انجام شد.' , 'status'=>1]);
            }elseif($result['data']['code'] == 101){
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت به دلیل تکراری بودن تراکنش ناموفق بود',
                ]);

                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'تراکنش تکراری است.' , 'status'=>0]);
            } else {
                $pay->update([
                    'status'=>2,
                    'pay_description'=>'پرداخت هنگام تکمیل تراکنش با مشکل مواجه شد.',
                ]);

                return view('pay.payment' , ['time'=> $time , 'data'=> $data , 'msg'=>'خطا در تکمیل تراکنش ' , 'status'=>0]);
            }
        }
    }
}
