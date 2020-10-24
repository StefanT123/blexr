<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('no action')
                ->onUpdate('no action');
        });

        Schema::table('license_user', function (Blueprint $table) {
            $table->foreign('license_id')
                ->references('id')
                ->on('licenses')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');
        });

        Schema::table('work_from_home', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foreign_keys');
    }
}
