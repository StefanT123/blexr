<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->bigInteger('role_id')->unsigned();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('license_user', function (Blueprint $table) {
            $table->bigInteger('license_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->primary(['license_id', 'user_id']);
        });

        Schema::create('work_from_home', function (Blueprint $table) {
            $table->id();
            $table->text('date');
            $table->tinyInteger('hours')->unsigned();
            $table->boolean('approved')->nullable();
            $table->bigInteger('user_id')->unsigned();
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
        Schema::dropIfExists('roles');
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('licence_user');
        Schema::dropIfExists('work_from_home');
    }
}
