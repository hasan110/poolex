<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid');
            $table->bigInteger('video_id')->unsigned()->nullable();
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->string('title');
            $table->string('type');
            $table->integer('episode_number')->nullable();
            $table->string('poster')->nullable();
            $table->string('file')->nullable();
            $table->string('rate')->nullable();
            $table->integer('translate_status')->default(1);
            $table->text('description');
            $table->text('keywords')->nullable();
            $table->integer('status')->default(1);
            $table->integer('complete_link')->default(0);
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
        Schema::dropIfExists('videos');
    }
}
