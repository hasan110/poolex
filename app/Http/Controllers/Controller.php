<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Setting;
use App\Models\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getUserByToken($request)
    {
        $api_token = $request->header('x-api-key');

        $user = User::where('api_token' , $api_token)->first();

        return $user;
    }

    public function findUsersAsReferral($user , $count)
    {
        $today = Carbon::today();
        $user_referrals = $user->user_referrals()->where('expires_at' , '>' , $today)->pluck('referral_id')->toArray();

        if($user_referrals)
        {
            $users = User::whereNotIn('id', $user_referrals)->where('id' , '!=' , $user->id)->where('status' , 2)->inRandomOrder()->get();
        }else
        {
            $users = User::where('id' , '!=' , $user->id)->where('status' , 2)->inRandomOrder()->get();
        }

        $referrals = [];

        if($users)
        {
            foreach($users as $user)
            {
                $diff = $user->created_at->diffInDays($today);
                $average_views = round($user->views()->where('type' , 'video')->count() / $diff);
                if($average_views >= 20){
                    $referrals[] = $user;
                }

                if(count($referrals) == $count)
                {
                    break;
                }
            }
        }

        return $referrals;
    }

    public function validateData($request , $rules)
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'data'=> null,
                'message'=> null,
                'errors' => $validator->errors(),
            ],400);
        }

        return false;
    }

    public function uploadFile($file , $path)
    {
        if(env('DEPLOYED')){
            $file_path = base_path().'/uploads/'. $path;
        }else{
            $file_path = public_path().'/uploads/'. $path;
        }
        File::ensureDirectoryExists($file_path);
        $file_name = uniqid() . '.' .$file->getClientOriginalExtension();
        $file->move($file_path , $file_name);
        $db_path = $path .'/'. $file_name;
        return $db_path;

        // $file_name = uniqid() . '.' .$file->getClientOriginalExtension();
        // $image_path = 'uploads/'. $path .'/'. $file_name;
        // Storage::disk('ftp')->put($image_path, fopen($file, 'r+'));
        // $db_path = $path .'/'. $file_name;
        // return $db_path;
    }

    public function uploadUserFileBase64($file , $path)
    {
        $image_parts = explode(";base64,", $file);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        $file_name = uniqid() . '.'.$image_type;

        $image_path = $path .'/'. $file_name;
        if(env('DEPLOYED')){
            $file_path = base_path().'/uploads/'. $path;
        }else{
            $file_path = public_path().'/uploads/'. $path;
        }
        File::ensureDirectoryExists($file_path);
        file_put_contents($file_path . '/' . $file_name, $image_base64);

        // if upload using ftp
        // Storage::disk('ftp')->put('uploads/'.$image_path, fopen($file, 'r+'));

        return $image_path;
    }

    public function deleteFile($path)
    {
        if(env('DEPLOYED')){
            Storage::disk('ftp')->delete('uploads/'.$path);
        }else{
            File::delete(public_path().'/uploads/'.$path);
        }
        return true;
    }

    public function sendNotification(
        $for,
        $user,
        $title,
        $text,
        $section,
        $has_link,
        $model_id,
        $color
    ){

        $user ? $user_id = $user->id : $user_id = null;

        $notification = Notification::create([
            'for' => $for,
            'user_id' => $user_id,
            'title' => $title,
            'text' => $text,
            'section' => $section,
            'has_link' => $has_link,
            'model_id' => $model_id,
            'color' => $color
        ]);

        if(!$notification){
            return false;
        }

        return true;

    }

    public static function Cryptographer($type , $string)
    {
        $cipher = 'aes128';
        $iv = 'poolexencryption';

        if($type)
        {
            $output = openssl_encrypt($string , $cipher , 100 , 0 , $iv);
        }
        else
        {
            $output = openssl_decrypt($string , $cipher , 100 , 0 , $iv);
        }

        return $output;
    }

    public function settings()
    {
        return Setting::find(1);
    }

    public function sendSMS($receptor , $token1 , $token2 = null)
    {
        $username_sms = env('SMS_USERNAME');
        $password_sms = env('SMS_PASSWORD');
        $from = '50004001039009';
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
                    CURLOPT_POSTFIELDS => 'username='.$username_sms.'&password='.$password_sms.'&to='.$receptor.'&bodyId=70457&text='.$token1,
                    CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                )
            ));
            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            if ($error) {
                return false;
            }
        }
        return true;
    }
}
