<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
  /**
   * Display the specified resource.
   */
  public function show(Major $major)
  {
    $electiveSubjects = $major->getElectiveSubjectsInfo();
    return response()->json($electiveSubjects);
  }
}
