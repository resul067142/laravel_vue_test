<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sorular', function (Blueprint $table) {
            $table->id();

            $table->string('soru');
            $table->string('secenek_1');
            $table->string('secenek_2');
            $table->string('secenek_3');
            $table->string('secenek_4');
            $table->integer('dogru_cevap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sorular');
    }
};
