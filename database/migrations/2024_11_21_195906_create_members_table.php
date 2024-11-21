<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->text('details')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('occupation')->nullable();
            $table->string('contact')->nullable();
            $table->string('social_media')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            // Ebeveyn ilişkisi için foreign key
            $table->foreign('parent_id')->references('id')->on('members')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
}
