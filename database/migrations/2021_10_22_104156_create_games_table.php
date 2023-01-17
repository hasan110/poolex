<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid');

            $table->bigInteger('starter_id')->unsigned();
            $table->foreign('starter_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('acceptor_id')->unsigned()->nullable();
            $table->foreign('acceptor_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('game_number');
            $table->string('starter_score')->nullable();
            $table->string('acceptor_score')->nullable();
            $table->integer('incoming_coins')->nullable();
            $table->integer('received_coins')->nullable();
            $table->bigInteger('winner_id')->nullable();
            $table->integer('status')->default(0);
            $table->dateTime('expires_at');
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
        Schema::dropIfExists('games');
    }
}
