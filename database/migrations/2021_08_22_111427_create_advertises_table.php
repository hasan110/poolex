<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('number')->nullable();
            $table->string('instagram_id')->nullable();
            $table->string('link')->nullable();
            $table->text('description')->nullable();
            $table->string('picture')->nullable();
            $table->string('video')->nullable();
            $table->boolean('two_step')->default(0);
            $table->integer('type')->default(1);
            $table->integer('second_ad')->default(1);
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
        Schema::dropIfExists('advertises');
    }
}
