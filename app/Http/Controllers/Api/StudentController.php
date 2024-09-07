<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Grade\GradeService;
use App\Services\Major\MajorService;
use App\Services\Student\StudentService;
use App\Services\Recommendation\RecommendationService;

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
    protected GradeService $gradeService,
    protected RecommendationService $recommendationService
  ) {
    // 
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Student $student)
  {
    $data = $this->studentService->getStudentDataWithRecommendations($student);
    return response()->json($data);
  }

  /**
   * Display the specified student.
   *
   * @param int $student_id
   * @return \Illuminate\Http\JsonResponse
   */
  public function show($student_id)
  {
    $data = $this->studentService->getStudentDetailedInfo($student_id);
    return response()->json($data);
  }

  /**
   * Display a listing of the recommended subjects for the specified student.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\Models\Student $student
   * @return \Illuminate\Http\JsonResponse
   */
  public function courses(Request $request, Student $student)
  {
    $sksFilter = $request->input('sks');
    $gradeFilter = $request->input('grade', '');

    $recommendedSubjects = $this->recommendationService->getRecommendedSubjects($student, $sksFilter, $gradeFilter);

    return response()->json($recommendedSubjects);
  }
}
