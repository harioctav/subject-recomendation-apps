<?php

use App\Helpers\Enums\GenderType;
use App\Helpers\Enums\ReligionType;
use App\Helpers\Enums\StudentStatusType;
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
      $table->foreignId('village_id')->nullable()->constrained('villages')->onDelete('cascade');
      $table->string('nim')->unique();
      $table->string('nik')->unique()->nullable();
      $table->string('name');
      $table->string('email')->unique()->nullable();
      $table->date('birth_date')->nullable();
      $table->string('birth_place')->nullable();
      $table->enum('gender', GenderType::toArray())->default('unknown');
      $table->string('phone')->unique()->nullable();
      $table->enum('religion', ReligionType::toArray())->default('unknown');
      $table->string('initial_registration_period')->nullable();
      $table->string('origin_department')->nullable();
      $table->string('upbjj')->nullable();
      $table->text('address')->nullable();
      $table->enum('status', StudentStatusType::toArray())->default('unknown');
      $table->longText('avatar')->nullable();
      $table->string('parent_name')->nullable();
      $table->string('parent_phone_number')->unique()->nullable();
      $table->timestamps();
      $table->softDeletes();
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
