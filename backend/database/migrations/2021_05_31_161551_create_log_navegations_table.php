<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogNavegationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_navegations', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->string('route');
            $table->string('method');
            $table->unsignedBigInteger('id_user');
            $table->timestamps();
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_navegations');
    }
}
