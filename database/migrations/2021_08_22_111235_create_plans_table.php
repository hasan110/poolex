<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Plan;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('watch_per_ad');
            $table->integer('subset_watch_per_ad');
            $table->integer('max_referral');
            $table->integer('subset_rent_time');
            $table->integer('harvest_time');
            $table->integer('referral_cost_coin');
            $table->integer('referral_cost_cash');
            $table->integer('offer_coin')->nullable();
            $table->integer('offer_cost')->nullable();
            $table->integer('discount');
            $table->integer('validity')->nullable();
            $table->integer('price');
            $table->timestamps();
        });

        Plan::create([
            'title' => 'پلن برنزی',
            'watch_per_ad' => 300,
            'subset_watch_per_ad' => 16,
            'max_referral' => 150,
            'subset_rent_time' => 10,
            'harvest_time' => 12,
            'discount' => 30,
            'validity' => null,
            'price' => 0,
        ]);
        Plan::create([
            'title' => 'پلن نقره ای',
            'watch_per_ad' => 400,
            'subset_watch_per_ad' => 20,
            'max_referral' => 300,
            'subset_rent_time' => 8,
            'harvest_time' => 10,
            'discount' => 25,
            'validity' => 9,
            'price' => 10000,
        ]);
        Plan::create([
            'title' => 'پلن طلایی',
            'watch_per_ad' => 600,
            'subset_watch_per_ad' => 30,
            'max_referral' => 500,
            'subset_rent_time' => 7,
            'harvest_time' => 8,
            'discount' => 20,
            'validity' => 12,
            'price' => 20000,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
