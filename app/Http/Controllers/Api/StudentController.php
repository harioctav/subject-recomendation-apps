<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Services\Major\MajorService;
use App\Services\Student\StudentService;
use Illuminate\Support\Facades\Cache;

class StudentController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected MajorService $majorService,
    protected StudentService $studentService,
  ) {
    // 
  }

  /**
   * Display a listing of the resource.
   */
  public function index($major_id)
  {
    $student = $this->studentService->getWhere(
      wheres: [
        'major_id' => $major_id
      ],
      orderBy: 'name',
      orderByType: 'asc',
    )->get();

    return response()->json($student);
  }
}
