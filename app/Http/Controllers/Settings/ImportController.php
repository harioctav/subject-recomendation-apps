<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Imports\ImportRequest;
use App\Imports\Settings\MasterDataImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
  public function store(ImportRequest $request)
  {
    $payload = $request->validated();

    $file = $payload['file'];

    try {
      $importer = new MasterDataImport;
      Excel::import($importer, $file);

      $errors = $importer->getErrors();
      $feedbackMessage = $importer->getFeedbackMessage();

      // If you want to redirect back with a flash message
      return redirect(route('home'))->with('success', $feedbackMessage);
    } catch (\Exception $e) {
      // Handle any exceptions that weren't caught in the importer
      return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
    }
  }
}
