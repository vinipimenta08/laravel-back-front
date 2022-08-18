<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableListCustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('mysql2')->create('list_custom', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_campaign');
            $table->integer('ddd');
            $table->integer('phone');
            $table->string('message_sms', 130);
            $table->date('date_event');
            $table->string('title', 50);
            $table->longText('description');
            $table->string('location', 100)->nullable();
            $table->string('joker_one')->nullable();
            $table->string('joker_two')->nullable();
            $table->string('identification')->comment('id de identificação do cliente');
            $table->unsignedBigInteger('id_send_sms')->comment('status de enviado para o cliente do sms de envio');
            $table->string('id_sms')->comment('id do lote de envio sms');
            $table->unsignedBigInteger('id_status_link')->default(1);
            $table->string('hash');
            $table->timestamp('sended_at')->nullable();
            $table->timestamps();

            $table->foreign('id_client')->references('id')->on('clients');
            $table->foreign('id_campaign')->references('id')->on('campaigns');
            $table->foreign('id_send_sms')->references('id')->on('send_sms');
            $table->foreign('id_status_link')->references('id')->on('status_links');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::connection('mysql2')->dropIfExists('list_custom');
    }
}
