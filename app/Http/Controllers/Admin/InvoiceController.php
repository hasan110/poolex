<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Morilog\Jalali\Jalalian;

class InvoiceController extends Controller
{
    public $pagination = 50;

    public function list(Request $request)
    {
        $list = Invoice::with(['items' , 'user' , 'seller' , 'store']);

        $search_key = $request->search_key;
        if($search_key){
            $list = $list->where(function($query) use ($search_key) {
                $query->where('fullname','like', '%' . $search_key . '%')
                ->orWhere('address','like', '%' . $search_key . '%')
                ->orWhere('postal_code','like', '%' . $search_key . '%')
                ->orWhere('call_number','like', '%' . $search_key . '%');
            });
        }

        if($request->has('sort'))
        {
            $sort = $request->sort;

            if($sort == 'invoice_amount'){
                $list = $list->orderBy('invoice_amount' , 'asc');
            }
            elseif($sort == 'total_amount'){
                $list = $list->orderBy('total_amount' , 'asc');
            }
            elseif($sort == 'oldest'){
                $list = $list->orderBy('created_at' , 'asc');
            }
            elseif($sort == 'newest'){
                $list = $list->orderBy('created_at' , 'desc');
            }
        }else{
            $list = $list->orderBy('created_at' , 'desc');
        }

        if($request->has('status'))
        {
            $list = $list->where('status' , $request->status);
        }

        $list = $list->paginate($this->pagination);

        return Response::success($list ,'لیست سفارشات با موفقیت دریافت شد .');
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
        $invoice = Invoice::find($request->id);
        if(!$invoice)
        {
            return Response::error(null , 'سفارش یافت نشد.' , [] , 404);
        }

        if($status == 4 && $invoice->status == 3)
        {
            $invoice->update([
                'status'=>$status
            ]);
            $this->sellerCheckout($invoice);
            return Response::success(null , 'وضعیت سفارش با موفقیت تغییر کرد.');
        }

        return Response::error(null , 'خطایی در تغییر وضعیت سفارش رخ داده است.' , []);
    }

    public function sellerCheckout($invoice)
    {
        $porsant = 3; // برحسب درصد
        $porsant_percent = $porsant / 100;
        $porsant_top = 15000; // پورسانت تا سقف

        $total = intval($invoice->invoice_amount);

        $porsant_amount = $total * $porsant_percent;

        if($porsant_amount > $porsant_top)
        {
            $porsant_amount = $porsant_top;
        }

        $seller_amount = $total - $porsant_amount;

        $seller = $invoice->seller;

        if($seller)
        {
            $seller->update([
                'income' => $seller->income + $seller_amount
            ]);
        }

        return true;
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
}
