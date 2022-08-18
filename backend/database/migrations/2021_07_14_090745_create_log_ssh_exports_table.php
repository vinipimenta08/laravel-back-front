<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogSshExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('log_ssh_exports', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->default(1)->comment('1 - Information\n 2 - Error\n 3 - Warning');
            $table->string('message')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('init_process')->nullable();
            $table->text('init_search')->nullable();
            $table->string('end_search')->nullable();
            $table->timestamp('init_make_file')->nullable();
            $table->timestamp('end_make_file')->nullable();
            $table->timestamp('init_upload')->nullable();
            $table->timestamp('end_upload')->nullable();
            $table->index(['type']);
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
        Schema::connection('mysql2')->dropIfExists('log_ssh_exports');
    }
}
