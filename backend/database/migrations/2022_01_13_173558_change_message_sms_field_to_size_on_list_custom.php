<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMessageSmsFieldToSizeOnListCustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('list_custom', function (Blueprint $table) {
            $table->string('message_sms', 170)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('list_custom', function (Blueprint $table) {
            $table->string('message_sms', 130)->change();
        });
    }
}
