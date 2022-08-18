<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSmsSendingProgram extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create("sms_sending_program", function (Blueprint $table) {
            $table->id();
            $table->string('mailing_file_original', 255);
            $table->string('mailing_file_genion', 255);
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_campaign');
            $table->tinyInteger('researched')->default(0);
            $table->timestamp('programmed_at')->nullable();
            $table->boolean("active")->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_sms_sending_program');
    }
}
