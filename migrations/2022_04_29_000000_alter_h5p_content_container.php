<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){
        Schema::table('h5p_content_container', function(Blueprint $table){
            $table->integer('watermark_opacity')->unsigned();
        });
    }

    public function down(){
        Schema::table('h5p_content_container', function(Blueprint $table){
            $table->dropColumn('watermark_opacity');
        });
    }
};