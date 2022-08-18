<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_clients', function (Blueprint $table) {
            $db = DB::connection('mysql2')->getDatabaseName();
            $table->bigInteger('id_user')->unsigned();
            $table->bigInteger('id_client')->unsigned();
            $table->timestamps();
            $table->primary(['id_user', 'id_client']);
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_client')->references('id')->on($db.'.clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_clients');
    }
}
