<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateH5pContentContainerContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h5p_content_container_contents', function (Blueprint $table) {
            $table->bigInteger('container_id')->unsigned();
            $table->bigInteger('content_id')->unsigned()->onDelete('cascade');
            $table->foreign('container_id')->references('id')->on('h5p_content_container')->onDelete('cascade');
            $table->foreign('content_id')->references('id')->on('h5p_contents');
            $table->primary(['container_id', 'content_id'], 'fk_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('h5p_content_container_contents');
    }
}
