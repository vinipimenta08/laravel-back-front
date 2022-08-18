<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSMSCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('mysql2')->create('sms_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_campaigns');
            $table->integer('send')->default(0);
            $table->integer('deliver')->default(0);
            $table->integer('fail')->default(0);
            $table->integer('response')->default(0);
            $table->timestamps();

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
        Schema::connection('mysql2')->dropIfExists('sms_campaigns');
    }
}
