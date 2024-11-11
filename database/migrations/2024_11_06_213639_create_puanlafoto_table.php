<?php
// database/migrations/{timestamp}_create_puanlafoto_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePuanlafotoTable extends Migration
{
    public function up()
    {
        Schema::create('puanlafoto', function (Blueprint $table) {
            $table->id();
            $table->string('ad'); // name
            $table->string('dosya_yolu'); // path
            $table->unsignedTinyInteger('guzellik')->default(0); // beauty
            $table->unsignedTinyInteger('yetenek')->default(0); // skill
            $table->unsignedTinyInteger('kisisel')->default(0); // personality
            $table->decimal('ortalama_puan', 4, 2)->default(0.00); // average_score
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('puanlafoto');
    }
}

