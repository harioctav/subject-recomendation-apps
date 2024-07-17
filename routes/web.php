<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Settings\PasswordController;

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
  return view('welcome');
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
});
