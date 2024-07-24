<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Academics\MajorController;
use App\Http\Controllers\Academics\StudentController;
use App\Http\Controllers\Academics\SubjectController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Academics\MajorSubjectController;
use App\Http\Controllers\Evaluations\GradeController;
use App\Http\Controllers\Evaluations\RecommendationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
  return redirect(route('home'));
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware([
  'auth',
  'permission',
])->group(function () {
  // Settings Page
  Route::prefix('settings')->group(function () {
    // Role management.
    Route::resource('roles', RoleController::class)->except('show');

    // User management.
    Route::patch('users/status/{user}', [UserController::class, 'status'])->name('users.status');
    Route::resource('users', UserController::class)->except('show');
  });

  // Management password users.
  Route::get('users/password/{user}', [PasswordController::class, 'showChangePasswordForm'])->name('users.password');
  Route::post('users/password', [PasswordController::class, 'store']);
  Route::get('users/show/{user}', [UserController::class, 'show'])->name('users.show');

  Route::prefix('academics')->group(function () {
    // Major management
    Route::post('majors/import', [MajorController::class, 'import'])->name('majors.import');

    // Major to Subject Management
    Route::prefix('majors/{major}/subjects')->name('majors.subjects.')->group(function () {
      Route::get('create', [MajorSubjectController::class, 'create'])->name('create');
      Route::post('/', [MajorSubjectController::class, 'store'])->name('store');
      Route::delete('{subject}', [MajorSubjectController::class, 'destroy'])->name('destroy');
    });

    // Major
    Route::resource('majors', MajorController::class);

    // Subject management
    Route::post('subjects/import', [SubjectController::class, 'import'])->name('subjects.import');
    Route::resource('subjects', SubjectController::class);

    // Student management
    Route::resource('students', StudentController::class);
  });

  Route::prefix('evaluations')->group(function () {

    // Recommendations
    Route::resource('recommendations', RecommendationController::class);

    // Grades
    Route::resource('grades', GradeController::class)->except('show');
  });
});
