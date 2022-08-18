<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCheckExistsWhatsApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create("check_exists_whatsApp", function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('id_genion');
            $table->integer('ddd');
            $table->string('phone', 20);
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
        Schema::dropIfExists('check_exists_whatsApp');
    }
}
