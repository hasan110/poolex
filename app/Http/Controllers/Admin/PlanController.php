<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Morilog\Jalali\Jalalian;
use App\Models\Plan;
use Carbon\Carbon;

class PlanController extends Controller
{   
    public function list(Request $request)
    {
        $plans = Plan::all();
        
        return Response::success($plans ,'لیست پلن ها با موفقیت دریافت شد .');
    }

    public function get(Request $request)
    {
        $plan = Plan::find($request->id);
        
        return Response::success($plan ,' پلن  .');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'watch_per_ad' => ['required'],
            'subset_watch_per_ad' => ['required'],
            'max_referral' => ['required'],
            'subset_rent_time' => ['required'],
            'harvest_time' => ['required'],
            'referral_cost_coin' => ['required'],
            'referral_cost_cash' => ['required'],
            'offer_coin' => ['required'],
            'offer_cost' => ['required'],
            'discount' => ['required'],
            'price' => ['required']
        ]);
        $plan = Plan::find($request->id);

        $plan->update($request->all());

        return Response::success($plan ,' پلن  .');
    }
}