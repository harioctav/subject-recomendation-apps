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

Route::prefix('locations')->group(function () {
  Route::get('provinces', [LocationController::class, 'provinces'])->name('locations.provinces');
  Route::get('regencies/{province_id}', [LocationController::class, 'regencies'])->name('locations.regencies');
  Route::get('districts/{regency_id}', [LocationController::class, 'districts'])->name('locations.districts');
  Route::get('villages/{disctrict_id}', [LocationController::class, 'villages'])->name('locations.villages');
  Route::get('villages/post_code/{village_id}', [LocationController::class, 'postCode'])->name('locations.postCodes');
});

Route::name('api.')->group(function () {
  Route::prefix('students')->group(function () {
    Route::get('{major_id}', [StudentController::class, 'index'])->name('students.index');
  });

  Route::prefix('subjects')->group(function () {
    Route::get('{student}', [SubjectController::class, 'index'])->name('subjects.index');
  });
});
