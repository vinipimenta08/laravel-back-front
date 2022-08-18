<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('mysql2')->create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_client');
            $table->string('name', 100);
            $table->boolean("active")->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_client')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('campaigns');   
    }
}
