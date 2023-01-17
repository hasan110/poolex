<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('ticket_id')->unsigned();
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('admin_id')->unsigned()->nullable();
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');

            $table->text('text');
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
        Schema::dropIfExists('ticket_replies');
    }
}
