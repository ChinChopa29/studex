<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_task_id')->constrained()->onDelete('cascade');
            $table->text('text');
            $table->boolean('shuffle_answers')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_questions');
    }
};