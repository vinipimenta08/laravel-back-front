<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBillingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('customer_billing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_client');
            $table->decimal('value', 5, 2);
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
        Schema::dropIfExists('customer_billing');
    }
}
