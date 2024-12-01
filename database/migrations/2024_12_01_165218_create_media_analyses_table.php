<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaAnalysesTable extends Migration
{
    public function up()
    {
        Schema::create('media_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('file_paths');
            $table->json('faces');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_analyses');
    }
}
