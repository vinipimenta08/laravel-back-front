<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogImportErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('log_import_errors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_campaigns');
            $table->integer('line_file');
            $table->string('name_file')->nullable();
            $table->integer('qtd_errors');
            $table->string('fields_errors');
            $table->timestamp('date_input');
            $table->timestamps();
            $table->foreign('id_client')->references('id')->on('clients');
            $table->foreign('id_campaigns')->references('id')->on('campaigns');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('log_import_errors');
    }
}
