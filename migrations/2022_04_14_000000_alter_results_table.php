<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('h5p_results', function(Blueprint $table) {
            //TODO: should container_id not be nullable?!
            $table->bigInteger('container_id')->unsigned()->nullable();
            $table->foreign('container_id')->references('id')->on('h5p_content_container');

            $table->foreign('content_id')->references('id')->on('h5p_contents')->onDelete('cascade')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('h5p_results', function(Blueprint $table){
            $table->dropForeign('h5p_results_container_id_foreign');
            $table->dropForeign('h5p_results_content_id_foreign');
            $table->dropForeign('h5p_results_user_id_foreign');

            $table->dropColumn('container_id');
        });
    }
}
