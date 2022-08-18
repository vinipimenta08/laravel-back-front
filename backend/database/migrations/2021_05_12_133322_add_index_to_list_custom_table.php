<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToListCustomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('list_custom', function (Blueprint $table) {
            $table->index(['ddd', 'phone']);
        });

        Schema::connection('mysql2')->table('list_custom', function (Blueprint $table) {
            $table->index(['created_at']);
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
            $table->dropIndex(['ddd', 'phone']);
        });

        Schema::connection('mysql2')->table('list_custom', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
}
