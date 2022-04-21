<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('h5p_contents_user_data', function(Blueprint $table){
            DB::unprepared('ALTER TABLE `h5p_contents_user_data` DROP PRIMARY KEY');

            $table->id();
            //TODO: combined index?! - Should container_id have onDelete('cascade)
            $table->bigInteger('container_id')->unsigned()->nullable();
            $table->foreign('container_id')->references('id')->on('h5p_content_container');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('content_id')->references('id')->on('h5p_contents')->onDelete('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('h5p_contents_user_data', function(Blueprint $table){
            $table->dropForeign('h5p_contents_user_data_container_id_foreign');
            $table->dropForeign('h5p_contents_user_data_content_id_foreign');
            $table->dropForeign('h5p_contents_user_data_user_id_foreign');

            $table->dropColumn('container_id');
            $table->dropColumn('id');
            $table->primary(['content_id', 'user_id', 'sub_content_id', 'data_id'], 'fk_primary');
        });
    }
};
