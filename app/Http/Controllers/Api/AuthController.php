<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Models\VerifyCode;
use App\Models\User;
use App\Models\Plan;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendLoginVerifyCode;
use App\Mail\SendPassword;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function registerByNumber(Request $request)
    {
        $validation = $this->validateData($request , [
            'mobile_number' => 'required | numeric',
            'password' => 'required | min:6'
        ]);
        if($validation){
            return $validation;
        }

        $user = User::where('mobile_number' , $request->mobile_number)->first();
        if($user){
            return response()->json([
                'data'=> null,
                'message'=> 'شما قبلا ثبت نام کرده اید !',
                'errors' => null,
            ],400);
        }

        $username_sms = env('SMS_USERNAME');
        $password_sms = env('SMS_PASSWORD');
        $receptor = $request->mobile_number;
        $token = rand(100000,999999);
        $from = '50004001039009';
        $text = $token;
        if(env('DEPLOYED')){
            $curl = curl_init();
            curl_setopt_array($curl,
                array(
                    CURLOPT_URL => "http://api.payamak-panel.com/post/Send.asmx/SendByBaseNumber2",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => 'username='.$username_sms.'&password='.$password_sms.'&to='.$receptor.'&bodyId=70457&text='.$text,
                    CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                )
            ));
            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            if ($error) {
                return response()->json([
                    'data'=> null,
                    'message'=> 'مشکل در ارسال پیام پیش آمده است .',
                    'errors' => null,
                ],400);
            }
        }

        $code=new VerifyCode;
        $code->phone=$request->mobile_number;
        $code->password=$request->password;
        $code->code=$token;
        $code->save();

        return response()->json([
            'data'=> null,
            'message'=> 'پیامک احراز هویت با موفقیت ارسال شد .',
            'errors' => null,
        ],200);
    }

    public function numberVerify(Request $request)
    {
        $validation = $this->validateData($request , [
            'mobile_number' => 'required | numeric',
            'code' => 'required | digits:6',
        ]);
        if($validation){
            return $validation;
        }

        $verify_code = VerifyCode::where('phone', $request->mobile_number)->latest()->first();

        if (!$verify_code) {
            return response()->json([
                'data'=> null,
                'message'=> 'شماره تلفن نامعتبر',
                'errors' => null,
            ],400);
        }

        if ($verify_code->code !== $request->code) {
            return response()->json([
                'data'=> null,
                'message'=> 'کد وارد شده صحیح نمی باشد .',
                'errors' => null,
            ],400);
        }

        $user = User::where('mobile_number' , $verify_code->phone)->first();
        if($user){
            return response()->json([
                'data'=> null,
                'message'=> 'شما قبلا ثبت نام کرده اید !',
                'errors' => null,
            ],400);
        }

        $seller = 0;
        if($request->has('seller') && $request->seller == 1){
            $seller = 1;
        }

        $user = resolve(UserRepository::class)->createUserByNumber([
            'is_seller'=>$seller,
            'password'=>$verify_code->password,
            'mobile_number'=>$verify_code->phone
        ]);

        return response()->json([
            'data'=> $user,
            'message'=> 'کد وارد شده صحیح می باشد .',
            'errors' => null,
        ],200);
    }

    public function loginByNumber(Request $request)
    {
        $validation = $this->validateData($request , [
            'mobile_number' => 'required',
            'password' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $user = User::where('mobile_number' , $request->mobile_number)->first();
        if(!$user){
            return Response::error(null , 'کاربری با شماره وارد شده ثبت نام نکرده است !' , null);
        }

        $unhash = $this->Cryptographer(0 , $user->password);

        if($request->password !== $unhash)
        {
            return Response::error(null , 'رمز عبور اشتباه است !' , null);
        }

        return Response::success($user , 'ورود با موفقیت انجام شد .');
    }

    public function seller_login(Request $request)
    {
        $validation = $this->validateData($request , [
            'mobile_number' => 'required',
            'password' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $user = User::where('mobile_number' , $request->mobile_number)->first();
        if(!$user){
            return Response::error(null , 'کاربری با شماره وارد شده ثبت نام نکرده است !' , null);
        }

        $unhash = $this->Cryptographer(0 , $user->password);

        if($request->password !== $unhash)
        {
            return Response::error(null , 'رمز عبور اشتباه است !' , null);
        }

        if(!$user->is_seller)
        {
            return Response::error(null , 'شما دسترسی به پنل فروشندگان ندارید.' , null);
        }

        return Response::success($user , 'ورود با موفقیت انجام شد .');
    }

    public function send_password_by_number(Request $request)
    {
        $validation = $this->validateData($request , [
            'mobile_number' => 'required',
        ]);
        if($validation){
            return $validation;
        }

        $user = User::where('mobile_number' , $request->mobile_number)->first();
        if(!$user){
            return Response::error(null , 'شخصی تا کنون با این شماره ثبت نام نکرده است .' , null);
        }

        $unhash = $this->Cryptographer(false , $user->password);

        $username_sms = env('SMS_USERNAME');
        $password_sms = env('SMS_PASSWORD');
        $receptor = $user->mobile_number;
        $from = '50004001039009';
        $text = $unhash;

        if(env('DEPLOYED')){
            $curl = curl_init();
            curl_setopt_array($curl,
                array(
                    CURLOPT_URL => "http://api.payamak-panel.com/post/Send.asmx/SendByBaseNumber2",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => 'username='.$username_sms.'&password='.$password_sms.'&to='.$receptor.'&bodyId=78987&text='.$text,
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/x-www-form-urlencoded",
                    )
                )
            );
            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            if ($error) {
                return response()->json([
                    'data'=> null,
                    'message'=> 'مشکل در ارسال پیام پیش آمده است .',
                    'errors' => null,
                ],400);
            }
        }

        return Response::success(null , 'رمز عبور به شماره شما ارسال شد .');
    }

    public function register(Request $request)
    {
        $validation = $this->validateData($request , [
            'email' => 'required|email',
            'password' => 'required|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'required',
        ]);
        if($validation){
            return $validation;
        }

        $user = User::where('email' , $request->email)->count();
        if($user){
            return Response::error(null , 'شما قبلا ثبت نام کرده اید !' , null);
        }

        $token = rand(100000,999999);

        $code=new VerifyCode;
        $code->email=$request->email;
        $code->code=$token;
        $code->save();

        if(env('DEPLOYED')){
            Mail::to($request->email)->send(new SendLoginVerifyCode($code));
        }

        return Response::success(null , 'کد فعال سازی به ایمیل شما ارسال شد .');
    }

    public function login(Request $request)
    {
        $validation = $this->validateData($request , [
            'email' => 'required',
            'password' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $user = User::where('email' , $request->email)->first();
        if(!$user){
            return Response::error(null , 'کاربری با ایمیل وارد شده ثبت نام نکرده است !' , null);
        }

        $unhash = $this->Cryptographer(0 , $user->password);

        if($request->password !== $unhash)
        {
            return Response::error(null , 'رمز عبور اشتباه است !' , null);
        }

        return Response::success($user , 'ورود با موفقیت انجام شد .');
    }

    public function verify(Request $request)
    {
        $validation = $this->validateData($request , [
            'email' => 'required',
            'password' => 'required',
            'code' => 'required | digits:6',
        ]);
        if($validation){
            return $validation;
        }

        $verify_code = VerifyCode::where('email', $request->email)->latest()->first();

        if (!$verify_code) {
            return Response::error(null , 'ایمیل نامعتبر است .' , null);
        }

        if ($verify_code->code !== $request->code) {
            return Response::error(null , 'کد وارد شده صحیح نمی باشد .' , null);
        }

        $user_check = User::where('email' , $request->email)->count();
        if($user_check){
            return Response::error(null , 'شما قبلا ثبت نام کرده اید !' , null);
        }

        $user = resolve(UserRepository::class)->createUser($request);

        return Response::success($user , 'کد وارد شده صحیح می باشد .');
    }

    public function forget_password(Request $request)
    {
        $validation = $this->validateData($request , [
            'email' => 'required',
        ]);
        if($validation){
            return $validation;
        }

        $user = User::where('email' , $request->email)->first();
        if(!$user){
            return Response::error(null , 'شخصی تا کنون با این ایمیل ثبت نام نکرده است .' , null);
        }

        $unhash = $this->Cryptographer(false , $user->password);

        if(env('DEPLOYED')){
            Mail::to($request->email)->send(new SendPassword($unhash));
        }

        return Response::success(null , 'رمز عبور به ایمیل شما ارسال شد .');
    }

    public function change_password(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'password' => 'required',
            'new_password' => 'required'
        ]);
        if($validation){
            return $validation;
        }

        $unhash = $this->Cryptographer(false , $user->password);
        if($unhash !== $request->password){
            return Response::error(null , 'رمز عبور وارد شده اشتباه است .' , null);
        }

        $user->update([
            'password' => $this->Cryptographer(true , $request->new_password)
        ]);

        return Response::success(null , 'رمز عبور با موفقیت بروزرسانی شد .');
    }

    public function update_profile(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'fullname' => 'required',
            'mobile_number' => 'required',
            'birth_place' => 'required',
            'card_number' => 'required',
            'shaba_number' => 'required',
            'card_image' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        if($user->status == 3){
            $status = 3;
        }else{
            $status = 1;
        }
        $user->update([
            'fullname' => $request->fullname,
            'mobile_number' => $request->mobile_number,
            'birth_date' => $request->birth_date ? $request->birth_date : $user->birth_date,
            'birth_place' => $request->birth_place,
            'status' => $status,
        ]);

        if($request->hasFile('profile')){


            if($user->profile){
                // File::delete(public_path().'/uploads/'.$user->profile);
            }

            $user->update([
                'profile' => $this->uploadFile($request->profile , 'users/'.$user->id),
            ]);

        }

        $bank = $user->bank_account;
        if($bank)
        {
            $bank->update([
                'card_number' => $request->card_number,
                'shaba_number' => $request->shaba_number
            ]);
        }else{
            $bank = $user->bank_account()->create([
                'card_number' => $request->card_number,
                'shaba_number' => $request->shaba_number,
                'card_image' => ''
            ]);
        }

        if($request->hasFile('card_image')){
            if($bank->card_image){
                // File::delete(public_path().'/uploads/'.$bank->card_image);
            }
            $bank->update([
                'card_image' => $this->uploadFile($request->card_image , 'users/'.$user->id),
            ]);
        }

        $user = $this->getUserByToken($request);

        return Response::success($user , 'اطلاعات کاربر با موفقیت بروزرسانی شد .');
    }

    public function fast_update_profile(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'fullname' => 'required',
            'address' => 'required',
        ]);
        if($validation){
            return $validation;
        }
        $user->update([
            'fullname' => $request->fullname,
            'address' => $request->address
        ]);

        $user = $this->getUserByToken($request);

        return Response::success($user , 'پروفایل شما با موفقیت بروزرسانی شد .');
    }

    public function seller_update_profile(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'fullname' => 'required',
            'national_code' => 'required|numeric',
            'province' => 'required',
            'city' => 'required',
            'address' => 'required',
            'postal_code' => 'required',
        ]);
        if($validation){
            return $validation;
        }
        $user->update([
            'fullname' => $request->fullname,
            'national_code' => $request->national_code,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'postal_code' => $request->postal_code
        ]);

        $user = $this->getUserByToken($request);

        return Response::success($user , 'پروفایل شما با موفقیت بروزرسانی شد .');
    }

}
