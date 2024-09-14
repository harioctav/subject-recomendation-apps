<?php

namespace App\Http\Controllers\Academics;

use App\Models\Major;
use App\Models\Subject;
use App\Traits\ChacesData;
use App\Http\Controllers\Controller;
use App\Services\Major\MajorService;
use App\Helpers\Enums\SemesterLevelType;
use App\Services\Subject\SubjectService;
use App\Http\Requests\Academics\MajorSubjectRequest;
use Maatwebsite\Excel\Facades\Excel;

class MajorSubjectController extends Controller
{
  use ChacesData;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected MajorService $majorService,
    protected SubjectService $subjectService,
  ) {
    // 
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(Major $major)
  {
    // Ambil semua subject yang belum ditambahkan ke major tersebut
    $subjects = Subject::whereNotIn('id', function ($query) use ($major) {
      $query->select('subject_id')
        ->from('major_subject')
        ->where('major_id', $major->id);
    })->get();

    $semesters = $this->cacheData('subject_semesters', function () {
      return SemesterLevelType::toArray();
    });

    return view('academics.major_subjects.create', compact('subjects', 'major', 'semesters'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(MajorSubjectRequest $request, Major $major)
  {
    $this->majorService->handleStoreSubjectToMajorData($request, $major);
    return redirect(route('majors.show', $major))->withSuccess(trans('session.create'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Major $major, Subject $subject)
  {
    return $this->majorService->handleDestroySubjectToMajorData($major, $subject);
  }
}
