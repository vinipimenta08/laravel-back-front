<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipToLogLinkSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('log_link_sms', function (Blueprint $table) {
            $table->unsignedBigInteger('id_list_custom')->change();
            $table->foreign('id_list_custom')->references('id')->on('list_custom');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('log_link_sms', function (Blueprint $table) {
            $table->dropForeign('id_list_custom');
        });
    }
}
