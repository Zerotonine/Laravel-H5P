<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

return new class extends Migration {
    public function up(){
        Schema::table('h5p_content_container', function(Blueprint $table){
            $table->text('background_path')->nullable()->change();
            $table->text('watermark_path')->nullable()->change();
            $table->integer('watermark_opacity')->unsigned()->default('100')->change();
            $table->text('multipliers')->nullable()->change();
        });
    }

    public function down(){
        Schema::table('h5p_content_container', function(Blueprint $table){
            $table->text('background_path')->nullable(false)->change();
            $table->text('watermark_path')->nullable(false)->change();
            $table->integer('watermark_opacity')->unsigned()->default(false)->change();
            $table->text('multipliers')->nullable(false)->change();
        });
    }
};