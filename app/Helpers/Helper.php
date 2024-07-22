<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class Helper
{
  // Global Constant
  public const ALL = 'Semua Data';
  public const DEFAULT_PASSWORD = 'password';
  public const NEW_PASSWORD = 'password@baru123';

  /**
   * Check permission to action datatables;
   *
   * @param  mixed $permissions
   * @return bool
   */
  public static function checkPermissions(array $permissions = []): bool
  {
    if (me()->canAny($permissions)) :
      return true;
    endif;

    return false;
  }

  /**
   * Helper to Upload Files.
   */
  public static function uploadFile(
    Request $request,
    string $filePath,
    string $currentFilePath = null
  ) {
    if ($request->file('file')) {
      if ($currentFilePath) {
        Storage::delete($currentFilePath);
      }
      return Storage::putFile("public/{$filePath}", $request->file('file'));
    } elseif ($currentFilePath) {
      return $currentFilePath;
    } else {
      return null;
    }
  }

  public static function convertFormulas($file)
  {
    // Load the spreadsheet
    $spreadsheet = IOFactory::load($file);

    // Get the active sheet (modify as necessary)
    $sheet = $spreadsheet->getActiveSheet();

    // Iterate over all cells with formulas
    foreach ($sheet->getCoordinates() as $coordinate) {
      $cell = $sheet->getCell($coordinate);
      if ($cell->isFormula()) {
        // Replace formula with its calculated value
        $cell->setValue($cell->getCalculatedValue());
      }
    }

    // Save the modified spreadsheet
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $newFilePath = 'path_to_save_converted_file.xlsx';
    $writer->save($newFilePath);

    return $newFilePath;
  }
}
