<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends  Migration {
    public function up(){
        Schema::table('h5p_contents_user_data', function(Blueprint $table){
            $table->dropForeign('h5p_contents_user_data_container_id_foreign');
            $table->foreign('container_id')->references('id')->on('h5p_content_container')->onDelete('cascade')->change();
        });
    }

    public function down(){
        Schema::table('h5p_contents_user_data', function(Blueprint $table){
            $table->dropForeign('h5p_contents_user_data_container_id_foreign');
            $table->foreign('container_id')->references('id')->on('h5p_content_container')->onDelete('restrict')->change();
        });
    }
};