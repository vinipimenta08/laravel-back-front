<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIdUserFieldToNullableOnLogImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('log_imports', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('log_imports', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->change();
        });
    }
}
