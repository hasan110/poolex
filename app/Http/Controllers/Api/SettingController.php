<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function get_settings(Request $request)
    {
        $s = Setting::find(1);
        $s->allow_record_harvest = (int) $s->allow_record_harvest;
        $s->allow_watch_ads = (int) $s->allow_watch_ads;
        $s->allow_buy_referral = (int) $s->allow_buy_referral;
        $s->allow_convert = (int) $s->allow_convert;
        $s->allow_get_award = (int) $s->allow_get_award;
        $s->store_offer = (int) $s->store_offer;
        return Response::success($s , 'تنظیمات با موفقیت دریافت شد .');
    }
}
