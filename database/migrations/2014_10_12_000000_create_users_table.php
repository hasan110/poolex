<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('national_code')->nullable();
            $table->string('identifier_code')->nullable();
            $table->string('referral_id')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->integer('coins')->default(0);
            $table->integer('cash')->default(0);
            $table->integer('income')->default(0);
            $table->string('profile')->nullable();
            $table->integer('status')->default(0);
            $table->integer('is_seller')->default(0);
            $table->integer('ad_keys')->default(0);
            $table->string('reject_reason')->nullable();
            $table->string('fcm_refresh_token')->unique()->nullable();
            $table->string('api_token' , 120)->unique();
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
        Schema::dropIfExists('users');
    }
}
