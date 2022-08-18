<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogErrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('log_erros', function (Blueprint $table) {
            $db = DB::connection('mysql')->getDatabaseName();
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->text('Message');
            $table->integer('Code');
            $table->string('File');
            $table->integer('Line');
            $table->text('TraceAsString');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on(new Expression($db . '.users'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('log_erros');
    }
}
