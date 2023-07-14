<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class UserController extends Controller
{
    public $pagination = 50;

    public function list(Request $request)
    {
        $users = User::whereNotNull('id');

        if($request->search_key){
            $search_key = $request->search_key;
            $users = $users->where(function($query) use ($search_key) {
                $query->where('fullname','like', '%' . $search_key . '%')
                ->orWhere('email','like', '%' . $search_key . '%')
                ->orWhere('mobile_number','like', '%' . $search_key . '%')
                ->orWhere('username','like', '%' . $search_key . '%');
            });
        }

        if($request->has('sort'))
        {
            $sort = $request->sort;

            if($sort == 'name'){
                $users = $users->orderBy('fullname' , 'asc');
            }
            elseif($sort == 'newest'){
                $users = $users->orderBy('created_at' , 'desc');
            }
            elseif($sort == 'oldest'){
                $users = $users->orderBy('created_at' , 'asc');
            }
            elseif($sort == 'confirmed_users'){
                $users = $users->where('status' , 2);
            }
            elseif($sort == 'rejected_users'){
                $users = $users->where('status' , 3);
            }
            elseif($sort == 'pending_users'){
                $users = $users->where('status' , 1);
            }
            elseif($sort == 'has_income'){
                $users = $users->where('income' , '>' , 0);
            }
        }else{
            $users = $users->orderBy('created_at' , 'desc');
        }

        if($request->has('is_seller'))
        {
            $users = $users->where('is_seller' , 1);
        }

        $users = $users->paginate($this->pagination);

        foreach($users as $user){
            $user['registered_at'] = Jalalian::forge($user->created_at)->format('%d / %m / %Y');
        }

        return Response::success($users ,'لیست کاربران با موفقیت دریافت شد .');
    }

    public function get(Request $request)
    {
        $user = User::whereId($request->id)->with(['user_plan' , 'bank_account' , 'user_referrals' , 'user_awards' , 'views'])->first();

        if($user->bank_account){

            $user->bank_account['shamsi_created_at'] = Jalalian::forge($user->bank_account->created_at)->format('%Y/%m/%d - %H:i');

        }
        if($user->user_referrals){

            foreach($user->user_referrals as $item){
                $referral = User::find($item->referral_id);
                $ref_today_views = 0;
                if($referral){
                    $item['referral'] = $referral;
                    if($referral->views){
                        foreach($referral->views as $ref_item){
                            if($ref_item->created_at > Carbon::today())
                            {
                                $ref_today_views++;
                            }
                        }

                    }
                }
                $item['ref_today_views'] = $ref_today_views;
                $item['create'] = Jalalian::forge($item->created_at)->format('%Y/%m/%d - %H:i');
                $item['expire'] = Jalalian::forge($item->expires_at)->format('%Y/%m/%d - %H:i');
            }

        }
        if($user->user_awards){

            foreach($user->user_awards as $item){
                $item['award'] = $item->award;
            }

        }
        $all_views = 0;
        $today_views = 0;
        if($user->views){
            foreach($user->views as $item){
                $item['advertise'] = $item->advertise;
                $all_views++;
                if($item->created_at > Carbon::today())
                {
                    $today_views++;
                }
            }

        }
        $user['all_views'] = $all_views;
        $user['today_views'] = $today_views;

        if($user->user_plan->expiry){
            $user->user_plan->expire = Jalalian::forge($user->user_plan->expiry)->format('%Y/%m/%d - %H:i');
        }else{
            $user->user_plan->expire = null;
        }

        $user['plan'] = $user->user_plan->plan;
        $user['harvests_count'] = $user->harvests()->count();

        return Response::success($user ,'اطلاعات کاربر با موفقیت دریافت شد .');
    }

    public function operate(Request $request)
    {
        $request->validate([
            'status' => ['required'],
            'id' => ['required']
        ]);
        $status = $request->status;
        $user = User::find($request->id);

        $user->update([
            'status'=>$status,
            'reject_reason'=>$request->reject_reason ? $request->reject_reason : null
        ]);

        return Response::success($user ,'تغییر وضعیت با موفقیت انجام شد .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'fullname' => ['required'],
            'birth_date' => ['required'],
            'birth_place' => ['required'],
        ]);

        $user = User::find($request->id);

        $user->update([
            'fullname'=>$request->fullname,
            'birth_date'=>$request->birth_date,
            'birth_place'=>$request->birth_place,
            'identifier_code'=>$request->identifier_code,
            'is_seller'=>isset($request->is_seller) && $request->is_seller ? 1 : 0,
            'email'=>$request->email
        ]);

        return Response::success($user ,'ویرایش کاربر با موفقیت انجام شد .');
    }

    public function chargeUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'to' => 'required',
            'type' => 'required',
            'amount' => 'required'
        ]);

        $user = User::find($request->user_id);

        if(!$user){
            return response()->json([
                'data'=> null,
                'message'=> 'ناموفق.',
                'errors' => null,
            ],400);
        }

        $amount = $request->amount;
        // to if true equals tether else rial
        if($request->to)
        {
            if($request->type)
            {
                $user->update([
                    'coins'=>$user->coins += $amount,
                ]);
            }else{
                if($user->coins < $amount){
                    return response()->json([
                        'data'=> null,
                        'message'=> 'کاربر به این میزان سکه ندارد.',
                        'errors' => null,
                    ],200);
                }
                $user->update([
                    'coins'=>$user->coins -= $amount,
                ]);
            }
        }else{
            if($request->type)
            {
                $user->update([
                    'cash'=>$user->cash += $amount,
                ]);
                $user->user_plan->update([
                    'income' => $user->user_plan->income += $amount
                ]);
            }else{
                if($user->cash < $amount){
                    return response()->json([
                        'data'=> $user,
                        'message'=> 'کاربر به این میزان شارژ ندارد.',
                        'errors' => null,
                    ],400);
                }
                $user->update([
                    'cash'=>$user->cash -= $amount,
                ]);
            }
        }

        return response()->json([
            'data'=> $user,
            'message'=> 'عملیات با موفقیت انجام شد.',
            'errors' => null,
        ],200);
    }

    public function giveAward(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'count' => 'required'
        ]);

        $user = User::find($request->user_id);

        if(!$user){
            return response()->json([
                'data'=> null,
                'message'=> 'ناموفق.',
                'errors' => null,
            ],400);
        }

        $count = $request->count;

        $user->user_plan->update([
            'award_paid_count'=>$user->user_plan->award_paid_count + $count
        ]);

        return response()->json([
            'data'=> $user,
            'message'=> 'عملیات با موفقیت انجام شد.',
            'errors' => null,
        ],200);
    }

    public function giveReferal(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'count' => 'required'
        ]);

        $user = User::find($request->user_id);
        if(!$user){
            return response()->json([
                'data'=> null,
                'message'=> 'ناموفق.',
                'errors' => null,
            ],400);
        }

        $settings = $this->settings();
        $count = $request->count;
        $user_plan = $user->user_plan;
        $plan = $user_plan->plan;
        $today = Carbon::today();

        if($count < 1)
        {
            return Response::error(null , 'درخواست نامعتبر..' , null);
        }

        // if($user_plan->can_rent_referral_at && $user_plan->can_rent_referral_at > $today)
        // {
        //     return Response::error(null , 'محدودیت اجاره زیرمجموعه به اتمام نرسیده است.' , null);
        // }

        $count_referrals = $user->user_referrals()->where('expires_at' , '>' , $today)->count();
        // if($count_referrals+$count > $plan->max_referral){
        //     return Response::error(null , 'تعداد زیرمجموعه انتخاب شده بیشتر از حد مجاز شده است .' , null);
        // }

        $referrals = $this->findUsersAsReferral($user , $count);

        if(count($referrals) < $count)
        {
            return Response::error(null , 'در حال حاضر درخواست شما مقدور نمی باشد.' , null);
        }

        $one_month_later = Carbon::today()->addDays(30);
        foreach($referrals as $item){
            $user->user_referrals()->create([
                'referral_id'=>$item['id'] ,
                'expires_at'=>$one_month_later
            ]);
        }

        return response()->json([
            'data'=> $user,
            'message'=> 'عملیات با موفقیت انجام شد.',
            'errors' => null,
        ],200);
    }

    public function updateKeys(Request $request)
    {
        $users = User::all();

        foreach ($users as $user)
        {
            if($user->ad_keys > 0){
                $user->update([
                    'ad_keys'=>0
                ]);
            }
        }

        return true;
    }

    public function checkoutSeller(Request $request)
    {
        $user = User::find($request->user_id);
        if(!$user){
            return response()->json([
                'data'=> null,
                'message'=> 'ناموفق.',
                'errors' => null,
            ],400);
        }
        $user->update([
            'income'=>0
        ]);
        return response()->json([
            'data'=> $user,
            'message'=> 'عملیات با موفقیت انجام شد.',
            'errors' => null,
        ],200);
    }
}
