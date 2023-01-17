<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('video_id')->unsigned()->nullable();
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');

            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('store_products')->onDelete('cascade');

            $table->enum('type' , ['like','dislike','comment','star']);
            $table->longText('text')->nullable();
            $table->integer('stars')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('feedback');
    }
}
