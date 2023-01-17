<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserReferral;
use App\Models\Advertise;
use App\Models\View;
use Carbon\Carbon;

class AdvertiseController extends Controller
{
    public function list(Request $request)
    {
        $user = $this->getUserByToken($request);
        $today = Carbon::today();
        $now = Carbon::now();

        $data = Advertise::where('status' , 1)->where('updated_at' , '>' , $today)->get();

        foreach($data as $item){

            $view = View::where('user_id' , $user->id)
            ->where('advertise_id' , $item->id)
            ->where('created_at' , '>=' , $item->updated_at)
            ->first();

            if($view){
                $item->seen = 1;
            }else{
                $item->seen = 0;
            }

        }

        return Response::success($data , 'لیست آگهی ها با موفقیت دریافت شد .');
    }

    public function get_ad(Request $request)
    {
        $user = $this->getUserByToken($request);

        $date = Carbon::today();
        $views = View::where('user_id', $user->id)->where('type', 'video')->where('updated_at' , '>' , $date)->pluck('advertise_id')->toArray();

        if($views){
            $ad = Advertise::whereNotIn('id', $views)->where('status' , 1)->where('updated_at' , '>' , $date)->first();
        }else{
            $ad = Advertise::where('status' , 1)->where('updated_at' , '>' , $date)->first();
        }

        $watched_ads = View::where('user_id', $user->id)->where('type', 'video')->where('updated_at' , '>' , $date)->count();
        $today_ads = Advertise::where('status' , 1)->where('updated_at' , '>' , $date)->count();
        $google_watched_ads = View::where('user_id', $user->id)->where('type', 'google_ads')->where('updated_at' , '>' , $date)->count();

        if(floor($watched_ads / 2) > $google_watched_ads)
        {
            $watch_now = 'google_ad';
        }else{
            $watch_now = 'ad';
        }
        $data = [
            'ad'=>$ad,
            'watched_ads'=>$watched_ads,
            'today_ads'=>$today_ads,
            'watch_now'=>$watch_now
        ];
        return Response::success($data , 'اطلاعات باموفقیت دریافت شد.');
    }

    public function get_ad_data(Request $request)
    {
        $validation = $this->validateData($request , [
            'id' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);

        $date = Carbon::today();
        $views = View::where('user_id', $user->id)->where('type', 'video')->where('updated_at' , '>' , $date)->pluck('advertise_id')->toArray();

        if($views){
            $ad = Advertise::whereNotIn('id', $views)->where('id' , $request->id)->where('status' , 1)->where('updated_at' , '>' , $date)->first();
            if(!$ad){
                return Response::error(null , 'درخواست نامعتبر.' , null);
            }
        }else{
            $ad = Advertise::where('status' , 1)->where('id' , $request->id)->where('updated_at' , '>' , $date)->first();
            if(!$ad){
                return Response::error(null , 'درخواست نامعتبر.' , null);
            }
        }

        $watched_ads = View::where('user_id', $user->id)->where('type', 'video')->where('updated_at' , '>' , $date)->count();
        $google_watched_ads = View::where('user_id', $user->id)->where('type', 'google_ads')->where('updated_at' , '>' , $date)->count();

        if(floor($watched_ads / 2) > $google_watched_ads)
        {
            $watch_now = 'google_ad';
        }else{
            $watch_now = 'ad';
        }
        $data = [
            'ad'=>$ad,
            'watch_now'=>$watch_now
        ];
        return Response::success($data , 'اطلاعات باموفقیت دریافت شد.');
    }

    public function ad_watched(Request $request)
    {
        $user = $this->getUserByToken($request);

        if($request->type == 'social_account' || $request->type == 'video' || $request->type == 'google_ads')
        {
            $type = $request->type;
        }else{
            return Response::error(null , 'درخواست نامعتبر.' , null);
        }

        if($type == 'google_ads'){
            // $add = View::create([
            //     'user_id'=>$user->id,
            //     'type'=>$type
            // ]);
            $user->update([
                'coins'=>$user->coins + 2
            ]);
            return Response::success(null , 'ok');
        }

        $ad = Advertise::where('id' , $request->id)->where('status' , 1)->first();
        if(!$ad){
            return Response::error(null , 'تبلیغ یافت نشد.' , null);
        }

        $date = Carbon::today();

        $view = View::where('user_id' , $user->id)
        ->where('advertise_id' , $request->id)
        ->where('type' , $type)
        ->where('created_at' , '>' , $date)
        ->first();
        if($view){
            return Response::error(null , 'این تبلیغ تماشا شده است.' , null);
        }

        // if($ad->two_step){
        //     if($type == 'social_account'){
        //         $zarib = 1;
        //     }else{
        //         $zarib = 1;
        //     }
        // }else{
        //     $zarib = 1;
        // }

        $add = View::create([
            'user_id'=>$user->id,
            'advertise_id'=>$request->id,
            'type'=>$type
        ]);

        $user_plan = $user->user_plan;
        $plan = $user_plan->plan;
        $add_cash = $plan->watch_per_ad;
        $user->update([
            'cash'=>$user->cash + $add_cash,
            'coins'=>$user->coins + 1,
            'ad_keys'=>$user->ad_keys + 1
        ]);

        $user->transactions()->create([
            'type'=>'cash',
            'amount'=>$add_cash
        ]);
        $user->transactions()->create([
            'type'=>'coin',
            'amount'=>1
        ]);

        $user->user_plan->update([
            'income' => $user->user_plan->income += $add_cash
        ]);

        $user_views_count = $user->views()->where('created_at' , '>=' , $date)->count();
        if($user_views_count >= 10){
            $kepts = $user->user_plan->kept_referrals_views;
            if($kepts > 0){

                $user->update([
                    'cash'=>$user->cash + ($plan->subset_watch_per_ad * $kepts)
                ]);

                $user->user_plan->update([
                    'income' => $user->user_plan->income + ($plan->subset_watch_per_ad * $kepts)
                ]);

            }

            $user->user_plan->update([
                'kept_referrals_views' => 0,
                'last_view_completion' => $date
            ]);

        }

        $referrals = UserReferral::where('referral_id' , $user->id)->where('expires_at' , '>' , $date)->get();

        if($referrals){
            foreach($referrals as $ref){
                $referral_user = User::find($ref->user_id);

                if($referral_user){

                    $referral_views_count = $referral_user->views()->where('created_at' , '>=' , $date)->count();
                    if($referral_views_count >= 10){

                        $user_plan = $referral_user->user_plan;
                        $plan = $user_plan->plan;
                        $adding_cash = $plan->subset_watch_per_ad;
                        $referral_user->update([
                            'cash'=>$referral_user->cash + $adding_cash
                        ]);

                        $referral_user->user_plan->update([
                            'income' => $referral_user->user_plan->income += $adding_cash
                        ]);
                    }else{
                        $referral_plan = $referral_user->user_plan->update([
                            'kept_referrals_views'=>$referral_user->user_plan->kept_referrals_views + 1
                        ]);
                    }

                }
            }
        }

        return Response::success(null , 'آگهی باموفقیت تماشا شد.');
    }
}
