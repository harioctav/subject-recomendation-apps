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
    Schema::create('villages', function (Blueprint $table) {
      $table->id();
      $table->string('uuid');
      $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
      $table->string('name');
      $table->string('code');
      $table->string('full_code');
      $table->string('pos_code');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('villages');
  }
};
