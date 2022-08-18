<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistListCustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('hist_list_custom', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_campaign');
            $table->string('name_client', 100)->nullable();
            $table->string('name_campaign', 100)->nullable();
            $table->bigInteger('base')->nullable();
            $table->bigInteger('sended')->nullable();
            $table->bigInteger('opening')->nullable();
            $table->bigInteger('imported')->nullable();
            $table->bigInteger('failed')->nullable();
            $table->bigInteger('reply')->nullable();
            $table->dateTime('sended_at')->nullable();
            $table->longText('location')->nullable();
            $table->bigInteger('total')->nullable();
            $table->longText('last_days')->nullable();
            $table->timestamps();

            $table->foreign('id_client')->references('id')->on('clients');
            $table->foreign('id_campaign')->references('id')->on('campaigns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('hist_list_custom');
    }
}
