<?php

use App\Helpers\Enums\GenderType;
use App\Helpers\Enums\ReligionType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('students', function (Blueprint $table) {
      $table->id();
      $table->string('uuid');
      $table->foreignId('major_id')->constrained('majors')->onDelete('cascade');
      $table->foreignId('village_id')->constrained('villages')->onDelete('cascade');
      $table->string('nim')->unique();
      $table->string('nik')->unique();
      $table->string('name');
      $table->string('email')->unique();
      $table->date('birth_date');
      $table->string('birth_place');
      $table->enum('gender', GenderType::toArray());
      $table->string('phone')->unique();
      $table->enum('religion', ReligionType::toArray());
      $table->string('initial_registration_period');
      $table->string('upbjj')->nullable();
      $table->text('address')->nullable();
      $table->text('note')->nullable();
      $table->longText('avatar')->nullable();
      $table->string('parent_name');
      $table->string('parent_phone_number')->unique();
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
