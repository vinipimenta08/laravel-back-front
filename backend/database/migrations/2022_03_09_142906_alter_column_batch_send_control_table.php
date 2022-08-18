<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnBatchSendControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('batch_send_control', function (Blueprint $table) {
            $table->string('mailing_file_original')->nullable()->change();
            $table->string('mailing_file_genion')->nullable()->change();
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
