<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('user_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        $admin = Admin::create([
            'name' => 'ادمین',
            'user_name' => 'poolex',
            'email' => 'poolex@gmail.com',
            'password' => Hash::make('12345')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
