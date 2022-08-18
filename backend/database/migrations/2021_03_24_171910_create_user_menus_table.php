<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_menus', function (Blueprint $table) {
            $db = DB::connection('mysql2')->getDatabaseName();
            $table->bigInteger('id_user')->unsigned();
            $table->bigInteger('id_menu')->unsigned();
            $table->timestamps();
            $table->primary(['id_user', 'id_menu']);
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_menu')->references('id')->on('menus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_menus');
    }
}
