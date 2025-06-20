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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('lastname');
            $table->string('iin', 12)->unique(); 
            $table->string('phone', 20); 
            $table->enum('gender', ['Мужской', 'Женский']);
            $table->date('birthday');
            $table->unsignedInteger('admission_year');
            $table->unsignedInteger('graduation_year');
            $table->unsignedBigInteger('education_program_id')->constrained('education_programs')->nullOnDelete(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
