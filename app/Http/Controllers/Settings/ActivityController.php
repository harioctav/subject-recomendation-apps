<?php

namespace App\Http\Controllers\Settings;

use App\DataTables\Settings\ActivityDataTable;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(ActivityDataTable $dataTable)
  {
    return $dataTable->render(
      'settings.activities.index'
    );
  }
}
