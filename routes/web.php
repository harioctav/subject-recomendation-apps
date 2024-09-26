<?php

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
use App\Http\Controllers\Settings\ActivityController;
use App\Http\Controllers\Settings\ImportController;

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

require __DIR__ . '/auth.php';

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware([
  'auth',
  'permission',
])->group(function () {
  // Activities
  Route::resource('activities', ActivityController::class)->only('index', 'show');

  // Settings Page
  Route::prefix('settings')->group(function () {
    // Role management.
    Route::resource('roles', RoleController::class)->except('show');

    // User management.
    Route::patch('users/status/{user}', [UserController::class, 'status'])->name('users.status');
    Route::resource('users', UserController::class)->except('show');

    // Import Management
    Route::controller(ImportController::class)
      ->prefix('imports')
      ->name('imports.')
      ->group(function () {
        Route::post('store', 'store')->name('store');
      });
  });

  // Management password users.
  Route::get('users/password/{user}', [PasswordController::class, 'showChangePasswordForm'])->name('users.password');
  Route::post('users/password', [PasswordController::class, 'store']);
  Route::get('users/show/{user}', [UserController::class, 'show'])->name('users.show');

  Route::prefix('academics')->group(function () {

    // Major to Subject Management
    Route::prefix('majors/{major}/subjects')->name('majors.subjects.')->group(function () {
      Route::controller(MajorSubjectController::class)
        ->group(function () {
          Route::post('/', 'store')->name('store');
          Route::get('create', 'create')->name('create');
          Route::delete('{subject}', 'destroy')->name('destroy');
        });
    });

    // Major
    Route::resource('majors', MajorController::class);

    // Subject management
    Route::resource('subjects', SubjectController::class);

    // Student management
    Route::prefix('students')->name('students.')
      ->controller(StudentController::class)->group(function () {
        Route::post('student-data-status', 'data')->name('data');
        Route::post('student-semester-remaining', 'semester')->name('semester');
        Route::post('import', 'import')->name('import');
        Route::put('{student}/restore', 'restore')->name('restore')->withTrashed();
        Route::delete('{student}/delete', 'delete')->name('delete')->withTrashed();
      });

    Route::resource('students', StudentController::class);
  });

  Route::prefix('evaluations')->group(function () {
    // Recommendations
    Route::prefix('recommendations')->name('recommendations.')
      ->controller(RecommendationController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('{student}/create', 'create')->name('create');
        Route::post('{student}/store', 'store')->name('store');
        Route::get('{student}/export', 'export')->name('export');
        Route::get('{student}/show', 'show')->name('show');
        Route::delete('{recommendation}', 'destroy')->name('destroy');
      });

    // Grades
    Route::prefix('grades')->name('grades.')
      ->controller(GradeController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('{student}/create', 'create')->name('create');
        Route::post('{student}/store', 'store')->name('store');
        Route::get('{grade}/{student}/edit', 'edit')->name('edit');
        Route::patch('{grade}/{student}/update', 'update')->name('update');
        Route::get('{student}/export', 'export')->name('export');
        Route::post('{student}/import', 'import')->name('import');
        Route::get('{student}/show', 'show')->name('show');
        Route::delete('{grade}', 'destroy')->name('destroy');
      });
  });
});
