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
            $table->string('image');
            $table->text('details')->nullable();
            $table->string('birthDate')->nullable();
            $table->string('occupation')->nullable();
            $table->json('hobbies')->nullable();
            $table->string('contact')->nullable();
            $table->string('socialMedia')->nullable();
            $table->string('generation');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
}
