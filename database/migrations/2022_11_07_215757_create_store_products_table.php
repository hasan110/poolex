<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('price');
            $table->string('price_before_off')->nullable();
            $table->string('discount')->nullable();
            $table->integer('inventory')->default(0);
            $table->text('files')->nullable();
            $table->text('description');
            $table->integer('views')->default(0);
            $table->integer('user_suggested')->default(0);
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
        Schema::dropIfExists('store_products');
    }
}
