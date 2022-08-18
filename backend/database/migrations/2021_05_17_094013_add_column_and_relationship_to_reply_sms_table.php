<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAndRelationshipToReplySmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('reply_sms', function (Blueprint $table) {
            $table->unsignedBigInteger('id_list_custom');
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
        Schema::connection('mysql2')->table('reply_sms', function (Blueprint $table) {
            $table->dropForeign('id_list_custom');
            $table->dropColumn('id_list_custom');
        });
    }
}
