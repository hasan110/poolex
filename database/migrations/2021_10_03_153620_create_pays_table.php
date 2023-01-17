<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('plan_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('amount');
            $table->text('token');
            $table->boolean('status')->default(0);
            $table->text('description')->nullable();
            $table->text('pay_description')->nullable();
            $table->dateTime('expire');
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
        Schema::dropIfExists('pays');
    }
}
