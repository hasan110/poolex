<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('allow_record_harvest')->default(0);
            $table->boolean('allow_watch_ads')->default(0);
            $table->boolean('allow_buy_referral')->default(0);
            $table->boolean('allow_convert')->default(0);
            $table->boolean('allow_get_award')->default(0);
            $table->integer('award_coin_reduction')->default(0);
            $table->integer('award_cash_reduction')->default(0);
            $table->integer('minimum_harvest_amount')->default(0);
            $table->integer('store_offer')->default(0);
            $table->integer('online_pay_award_cost')->default(0);
            $table->integer('vpn_required')->default(0);
            $table->string('stores_shipping_cost')->nullable();
            $table->text('about_us_text')->nullable();
            $table->text('store_rules_text')->nullable();
            $table->text('store_panel_rules_text')->nullable();
            $table->string('app_download_link')->nullable();
            $table->string('support_call_number')->nullable();
            $table->string('instagram_id')->nullable();
            $table->string('telegram_id')->nullable();
            $table->string('whatsup_number1')->nullable();
            $table->string('whatsup_number2')->nullable();
            $table->string('app_version_code')->nullable();
            $table->string('app_version_name')->nullable();
            $table->string('admob_app_id')->nullable();
            $table->string('admob_banner')->nullable();
            $table->string('admob_interstitial')->nullable();
            $table->string('admob_interstitial_video')->nullable();
            $table->string('admob_rewarded')->nullable();
            $table->string('admob_rewarded_interstitial')->nullable();
            $table->string('admob_native_ad')->nullable();
            $table->string('admob_app_open')->nullable();

            $table->string('ad_link1')->nullable();
            $table->string('ad_banner1')->nullable();
            $table->string('ad_link2')->nullable();
            $table->string('ad_banner2')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
