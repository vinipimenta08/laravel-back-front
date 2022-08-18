<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('log_imports', function (Blueprint $table) {
            $db = DB::connection('mysql')->getDatabaseName();
            $table->id();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_campaign');
            $table->integer('qtd_import');
            $table->integer('send_sms');
            $table->timestamps();

            
            $table->foreign('id_user')->references('id')->on(new Expression($db . '.users'));
            $table->foreign('id_client')->references('id')->on('clients');
            $table->foreign('id_campaign')->references('id')->on('campaigns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('log_imports');
    }
}
