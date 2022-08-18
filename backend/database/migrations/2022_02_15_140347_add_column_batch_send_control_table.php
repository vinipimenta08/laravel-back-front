<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnBatchSendControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('batch_send_control', function (Blueprint $table) {
            $table->string('mailing_file_original')->after('id');
            $table->string('mailing_file_genion')->after('mailing_file_original');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('batch_send_control', function (Blueprint $table) {
            $table->dropColumn('mailing_file_original');
            $table->dropColumn('mailing_file_genion');
        });
    }
}
