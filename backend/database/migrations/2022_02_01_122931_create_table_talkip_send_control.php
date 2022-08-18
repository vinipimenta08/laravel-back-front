<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTalkipSendControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('talkip_send_control', function (Blueprint $table) {
            $table->id();
            $table->string('id_talkip');
            $table->tinyInteger('verified')->default(0);
            $table->timestamp('search_date')->nullable();
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
        Schema::dropIfExists('talkip_send_control');
    }
}
