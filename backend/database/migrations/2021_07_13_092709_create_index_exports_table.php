<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndexExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('index_exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_list_custom');
            $table->timestamps();
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
        Schema::connection('mysql2')->dropIfExists('index_exports');
    }
}
