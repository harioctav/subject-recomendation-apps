<?php

namespace App\Http\Controllers\Grades;

use App\DataTables\Grades\RecommendationDataTable;
use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use App\Services\Recommendation\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    protected RecommendationService $recommendationService,
  ) {
    // 
  }

  /**
   * Display a listing of the resource.
   */
  public function index(RecommendationDataTable $dataTable)
  {
    return $dataTable->render('grades.recommendations.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
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
