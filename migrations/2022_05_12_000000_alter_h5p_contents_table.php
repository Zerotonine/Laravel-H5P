<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){
        Schema::table('h5p_contents', function(Blueprint $table){
            $table->longText('parameters')->change();
            $table->longText('filtered')->change();
        });
    }

    public function down(){
        Schema::table('h5p_contents', function(Blueprint $table){
            $table->text('parameters')->change();
            $table->text('filtered')->change();
        });
    }
};