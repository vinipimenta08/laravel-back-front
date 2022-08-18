<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplySmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('reply_sms', function (Blueprint $table) {
            $table->id();
            $table->string('id_reference');
            $table->string('phone', 11);
            $table->string('id_sms');
            $table->text('reply');
            $table->timestamp('received_at');
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
        Schema::connection('mysql2')->dropIfExists('reply_sms');
    }
}
