<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToLogSshExports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('log_ssh_exports', function (Blueprint $table) {
            $table->string('name_file')->after("description")->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('log_ssh_exports', function (Blueprint $table) {
            $table->dropColumn(['name_file']);
        });
    }
}
