<?php

use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Locations\LocationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::prefix('locations')
  ->controller(LocationController::class)
  ->name('locations.')
  ->group(function () {
    Route::get('provinces', 'provinces')->name('provinces');
    Route::get('regencies/{province_id}', 'regencies')->name('regencies');
    Route::get('districts/{regency_id}', 'districts')->name('districts');
    Route::get('villages/{disctrict_id}', 'villages')->name('villages');
    Route::get('villages/post_code/{village_id}', 'postCode')->name('postCode');
  });

Route::name('api.')->group(function () {
  Route::prefix('students')->group(function () {
    Route::get('{student}', [StudentController::class, 'courses'])->name('students.courses');
    Route::get('recommendations/{student}', [StudentController::class, 'index'])->name('students.index');
  });

  Route::prefix('subjects')->group(function () {
    Route::get('{student}', [SubjectController::class, 'index'])->name('subjects.index');
  });
});
