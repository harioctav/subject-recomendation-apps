<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Student;
use App\Services\Grade\GradeService;
use App\Services\Major\MajorService;
use App\Services\Recommendation\RecommendationService;
use App\Services\Student\StudentService;

class SubjectController extends Controller
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
  ) {
    // 
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Student $student)
  {
    $major = $this->majorService->findOrFail($student->major->id);
    $subjects = $major->subjects()->get();

    // Ambil ID matakuliah yang sudah diberi nilai
    $gradedSubjectIds = $this->gradeService->getWhere(
      wheres: [
        'student_id' => $student->id
      ]
    )->pluck('subject_id');

    // Filter out subjects that already have grades
    $filteredSubjects = $subjects->filter(function ($subject) use ($gradedSubjectIds) {
      return !$gradedSubjectIds->contains($subject->id);
    });

    // Kelompokkan subjects berdasarkan semester
    $groupedSubjects = $filteredSubjects->groupBy('pivot.semester');

    return response()->json($groupedSubjects);
  }
}
