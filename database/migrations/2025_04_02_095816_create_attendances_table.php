<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late'])->default('absent');
            $table->text('comment')->nullable();
            $table->timestamps();
            
            $table->unique(['lesson_id', 'student_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
