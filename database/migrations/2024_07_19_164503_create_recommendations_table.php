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
    Schema::create('recommendations', function (Blueprint $table) {
      $table->id();
      $table->string('uuid');
      $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
      $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
      $table->integer('semester');
      $table->string('exam_period')->nullable();
      $table->longText('note')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('recommendations');
  }
};
