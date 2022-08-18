<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableBatchSendControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->rename("talkip_send_control", "batch_send_control");

        Schema::connection('mysql2')->table('batch_send_control', function (Blueprint $table) {
            $table->renameColumn('id_talkip', 'id_sms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batch_send_control');
    }
}
