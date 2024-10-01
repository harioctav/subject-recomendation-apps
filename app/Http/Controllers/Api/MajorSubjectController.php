<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Http\Request;

class MajorSubjectController extends Controller
{
  public function index(Major $major, Request $request)
  {
    $term = $request->get('term');
    $subjects = Subject::whereNotIn('id', function ($query) use ($major) {
      $query->select('subject_id')
        ->from('major_subject')
        ->where('major_id', $major->id);
    })
      ->when($term, function ($query) use ($term) {
        return $query->where('name', 'LIKE', '%' . $term . '%');
      })
      ->select('id', 'name')
      ->get();

    return response()->json($subjects);
  }
}
