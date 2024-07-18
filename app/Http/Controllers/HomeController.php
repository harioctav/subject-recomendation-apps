<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\User\UserService;
use App\Helpers\Enums\GreetingType;
use App\Services\Major\MajorService;
use App\Services\Student\StudentService;
use App\Services\Subject\SubjectService;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected UserService $userService,
    protected MajorService $majorService,
    protected SubjectService $subjectService,
    protected StudentService $studentService,
  ) {
    $this->middleware('auth');
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    // Greetings
    $current = Carbon::now()->format('H');

    if ($current < 12) :
      $greeting = GreetingType::MORNING->value;
    elseif ($current < 18) :
      $greeting = GreetingType::EVENING->value;
    else :
      $greeting = GreetingType::NIGHT->value;
    endif;

    $data = [
      'users' => $this->userService->getQuery()->count(),
      'majors' => $this->majorService->getQuery()->count(),
      'subjects' => $this->subjectService->getQuery()->count(),
      'students' => $this->studentService->getQuery()->count(),
    ];

    return view('home', compact('data', 'greeting'));
  }
}
