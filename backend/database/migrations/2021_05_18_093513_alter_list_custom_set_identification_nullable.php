<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterListCustomSetIdentificationNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('list_custom', function (Blueprint $table) {
            $table->string('identification')->nullable(true)->comment('id de identificação do cliente')->change();
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
            $table->string('identification')->comment('id de identificação do cliente')->change();
        });
    }
}
