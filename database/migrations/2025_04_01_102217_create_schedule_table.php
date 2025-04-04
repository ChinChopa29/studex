<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('classroom')->nullable();
            
            $table->enum('type', [
                'lecture', 
                'practice', 
                'lab', 
                'seminar', 
                'exam', 
                'consultation'
            ])->default('lecture');
            
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            
            $table->enum('recurrence', ['none', 'weekly', 'biweekly'])->default('none');
            $table->date('recurrence_end_date')->nullable();
            
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('milestone_id')->constrained()->onDelete('cascade');

            $table->string('color')->default('#3b82f6');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule');
    }
};