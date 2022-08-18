<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordSendedMlGomes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('record_sended_ml_gomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_list_custom');
            $table->string('identification')->comment('id de identificação do cliente');
            $table->string('phone', 11);
            $table->timestamps();

            $table->foreign('id_list_custom')->references('id')->on('list_custom');

            $table->index(['identification', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('record_sended_ml_gomes');
    }
}
