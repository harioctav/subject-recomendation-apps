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
    Schema::create('districts', function (Blueprint $table) {
      $table->id();
      $table->string('uuid');
      $table->foreignId('regency_id')->constrained('regencies')->onDelete('cascade');
      $table->string('code');
      $table->string('name');
      $table->string('full_code');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('districts');
  }
};
