<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){
        Schema::table('h5p_content_container', function(Blueprint $table){
            $table->text('multipliers');
        });
    }

    public function down(){
        Schema::table('h5p_content_container', function(Blueprint $table){
            $table->dropColumn('multipliers');
        });
    }
};