<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignKeyTableReplySms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('reply_sms', function (Blueprint $table) {
            $table->dropForeign('reply_sms_id_list_custom_foreign');
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
            $table->dropForeign('reply_sms_id_list_custom_foreign');
        });
    }
}
