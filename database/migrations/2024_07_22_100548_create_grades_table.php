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
    Schema::create('grades', function (Blueprint $table) {
      $table->id();
      $table->string('uuid');
      $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
      $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
      $table->string('grade');
      $table->decimal('quality')->nullable();
      $table->decimal('mutu')->nullable();
      $table->string('exam_period');
      $table->string('note')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('grades');
  }
};
