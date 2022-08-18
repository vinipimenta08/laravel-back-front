<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('mysql2')->create('mailing_process', function (Blueprint $table) {
            $table->id();
            $table->string('mailing_file_original', 255);
            $table->string('mailing_file_genion', 255);
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_campaign');
            $table->integer('ddd')->nullable();
            $table->string('phone', 20)->nullable();
            $table->longText('message_sms')->nullable();
            $table->string('date_event', 20)->nullable();
            $table->longText('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('location')->nullable();
            $table->string('joker_one', 255)->nullable();
            $table->string('joker_two', 255)->nullable();
            $table->string('identification', 255)->comment('id de identificação do cliente')->nullable();
            $table->unsignedBigInteger('id_send_sms')->comment('status de enviado para o cliente do sms de envio');
            $table->string('id_sms')->comment('id do lote de envio sms');
            $table->unsignedBigInteger('id_status_link')->default(1);
            $table->string('hash');
            $table->tinyInteger('confirm_imported')->default(0);
            $table->timestamp('sended_at')->nullable();
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
        Schema::dropIfExists('mailing_process');
    }
}
