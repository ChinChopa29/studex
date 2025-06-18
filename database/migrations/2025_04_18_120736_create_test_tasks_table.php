<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('test_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('milestone_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('from');
            $table->dateTime('deadline');
            $table->boolean('shuffle_questions')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_tasks');
    }
};