<?php

namespace LaravelLaundromat\tests\testdata\migrations;

use Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('ssn');
            $table->date('birthday');
            $table->string('favorite_color');
            $table->string('password');
            $table->integer('family_id')->unsigned();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('family_id')->references('id')->on('families');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
