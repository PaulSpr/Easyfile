<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEasyFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('easyfiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('instance_id');
            $table->string('filename');
            $table->integer('size');
            $table->string('extension');
            $table->string('mimetype');
            $table->boolean('public');
            // for relations
            $table->integer('hasfile_id');
            $table->string('hasfile_type');
            $table->string('hasfile_attribute');

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
        Schema::drop('easyfiles');
    }
}
