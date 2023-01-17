<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Morilog\Jalali\Jalalian;

class SettingController extends Controller
{
    public function setting(Request $request)
    {
        $s = $this->settings();
        $s->allow_record_harvest = (int) $s->allow_record_harvest;
        $s->allow_watch_ads = (int) $s->allow_watch_ads;
        $s->allow_buy_referral = (int) $s->allow_buy_referral;
        $s->allow_convert = (int) $s->allow_convert;
        $s->allow_get_award = (int) $s->allow_get_award;
        $s->store_offer = (int) $s->store_offer;
        $s->vpn_required = (int) $s->vpn_required;
        return Response::success($s , 'تنظیمات با موفقیت دریافت شد .');
    }

    public function edit_setting(Request $request)
    {
        $setting = $this->settings();
        $setting->update($request->all());

        $setting = $this->settings();
        return Response::success($setting , 'تنظیمات با موفقیت بروز شد .');
    }

    public function upload_app_file(Request $request)
    {
        $setting = $this->settings();

        if($request->app_file)
        {
            $setting->update([
                'app_download_link' => $this->uploadFile($request->app_file , 'app')
            ]);
        }

        $setting = $this->settings();
        return Response::success($setting , 'فایل با موفقیت آپلود شد .');
    }

    public function edit_ads(Request $request)
    {
        $request->validate([
            'ad_banner1' => ['file'],
            'ad_banner2' => ['file'],
            'ad_link1' => ['required'],
            'ad_link2' => ['required']
        ]);

        $setting = $this->settings();
        $setting->update([
            'ad_link1' => $request->ad_link1,
            'ad_link2' => $request->ad_link2
        ]);

        if($request->ad_banner1)
        {
            $setting->update([
                'ad_banner1' => $this->uploadFile($request->ad_banner1 , 'settings'),
            ]);
        }

        if($request->ad_banner2)
        {
            $setting->update([
                'ad_banner2' => $this->uploadFile($request->ad_banner2 , 'settings'),
            ]);
        }

        $setting = $this->settings();
        return Response::success($setting , 'تنظیمات با موفقیت بروز شد .');
    }
}
