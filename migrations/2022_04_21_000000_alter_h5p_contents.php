<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(){
        Schema::table('h5p_contents', function(Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('library_id')->references('id')->on('h5p_libraries')->onDelete('cascade')->change();
        });
    }


    public function down(){
        Schema::table('h5p_contents', function(Blueprint $table){
            $table->dropForeign('h5p_contents_user_id_foreign');
            $table->dropForeign('h5p_contents_library_id_foreign');
        });
    }
};