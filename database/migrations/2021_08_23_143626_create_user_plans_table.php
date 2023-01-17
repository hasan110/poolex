<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('plan_id');
            $table->date('expiry')->nullable();
            $table->date('last_rent_referral')->nullable();
            $table->timestamp('last_message_seen')->nullable();
            $table->timestamp('can_use_offer_at')->nullable();
            $table->integer('award_paid_count')->default(0);
            $table->integer('harvest')->default(0);
            $table->integer('income')->default(0);
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
        Schema::dropIfExists('user_plans');
    }
}
