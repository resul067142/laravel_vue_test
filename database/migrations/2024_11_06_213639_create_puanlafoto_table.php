<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePuanlafotoTable extends Migration
{
       public function up()
    {
        Schema::create('puanlafoto', function (Blueprint $table) {
            $table->id(); // Benzersiz kimlik
            $table->string('ad')->comment('Fotoğraf adı'); // Fotoğraf adı
            $table->string('dosya_yolu')->comment('Dosya yolu'); // Fotoğraf dosya yolu
            $table->unsignedTinyInteger('guzellik')->default(0)->comment('Güzellik puanı'); // Güzellik
            $table->unsignedTinyInteger('yetenek')->default(0)->comment('Yetenek puanı'); // Yetenek
            $table->unsignedTinyInteger('kisisel')->default(0)->comment('Kişisel puanı'); // Kişisel
            $table->decimal('ortalama_puan', 4, 2)->default(0.00)->comment('Ortalama puan'); // Ortalama puan
            $table->timestamps(); // Oluşturulma ve güncellenme zaman damgaları
        });
    }
    public function down()
    {
        Schema::dropIfExists('puanlafoto'); // Tablonun silinmesi
    }
}
