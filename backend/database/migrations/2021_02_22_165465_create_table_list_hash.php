<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableListHash extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('mysql2')->create('list_hash', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_list_custom');
            $table->string('mailing_file_original', 255);
            $table->string('mailing_file_genion', 255);
            $table->string('hash');
            $table->unsignedBigInteger('id_status');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('list_hash');
    }
}
