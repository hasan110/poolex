<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use App\Models\StoreProduct;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class StoreProductController extends Controller
{
    public $pagination = 50;

    public function list(Request $request)
    {
        $list = StoreProduct::whereNotNull('id');

        if($request->search_key){
            $search_key = $request->search_key;
            $list = $list->where(function($query) use ($search_key) {
                $query->where('name','like', '%' . $search_key . '%')
                ->orWhere('slug','like', '%' . $search_key . '%')
                ->orWhere('description','like', '%' . $search_key . '%');
            });
        }

        if($request->has('sort'))
        {
            $sort = $request->sort;

            if($sort == 'newest'){
                $list = $list->orderBy('created_at' , 'desc');
            }
            elseif($sort == 'oldest'){
                $list = $list->orderBy('created_at' , 'asc');
            }
            elseif($sort == 'confirmed'){
                $list = $list->where('status' , 1);
            }
            elseif($sort == 'rejected'){
                $list = $list->where('status' , 2);
            }
            elseif($sort == 'pending'){
                $list = $list->where('status' , 0);
            }
        }else{
            $list = $list->orderBy('created_at' , 'desc');
        }

        $list = $list->paginate($this->pagination);

        foreach($list as $item){
            $item['date'] = Jalalian::forge($item->created_at)->format('%d / %m / %Y');
        }

        return Response::success($list ,'لیست محصولات با موفقیت دریافت شد .');
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
            'email'=>$request->email
        ]);

        return Response::success($user ,'ویرایش کاربر با موفقیت انجام شد .');
    }

    public function ChangeStatus(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
            'status' => ['required'],
        ]);

        $store_product = StoreProduct::find($request->product_id);

        $store_product->update([
            'status'=>$request->status
        ]);

        return Response::success($store_product ,'ویرایش محصول با موفقیت انجام شد .');
    }

}
