<?php

namespace App\Http\Controllers\Evaluations;

use Illuminate\Http\Request;
use App\Models\Recommendation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Services\Student\StudentService;
use App\Services\Recommendation\RecommendationService;
use App\DataTables\Evaluations\RecommendationDataTable;
use App\Http\Requests\Evaluations\RecommendationRequest;

class RecommendationController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected StudentService $studentService,
    protected RecommendationService $recommendationService,
  ) {
    // 
  }

  protected function students()
  {
    $students = $this->studentService->getWhere(
      orderBy: 'name',
      orderByType: 'asc',
    )->get();

    return $students;
  }

  /**
   * Display a listing of the resource.
   */
  public function index(RecommendationDataTable $dataTable)
  {
    return $dataTable->render('evaluations.recommendations.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $students = $this->students();
    return view('evaluations.recommendations.create', compact('students'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(RecommendationRequest $request)
  {
    $this->recommendationService->handleStoreData($request);
    return redirect(route('recommendations.index'))->withSuccess(trans('session.create'));
  }

  /**
   * Display the specified resource.
   */
  public function show(Recommendation $recommendation)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Recommendation $recommendation)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Recommendation $recommendation)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Recommendation $recommendation)
  {
    //
  }
}
