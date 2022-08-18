<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlacklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('blacklist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_client');
            $table->integer('ddd')->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean("active")->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blacklist');
    }
}
