<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\Helpers\Enums\GradeType;
use App\Models\Activity as ActivityModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Str;

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

  public static function generateQuality($grade)
  {
    if ($grade == GradeType::A->value) :
      return 4.00;
    elseif ($grade == GradeType::A_MIN->value) :
      return 3.70;
    elseif ($grade == GradeType::B->value):
      return 3.00;
    elseif ($grade == GradeType::B_MIN->value):
      return 2.70;
    elseif ($grade == GradeType::C->value):
      return 2.00;
    elseif ($grade == GradeType::C_MIN->value):
      return 1.70;
    elseif ($grade == GradeType::D->value):
      return 1.00;
    else:
      return 0.00;
    endif;
  }

  public static function calculateGPA($studentId)
  {
    // Ambil data nilai mahasiswa
    $grades = DB::table('grades')
      ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
      ->where('grades.student_id', $studentId)
      ->select('grades.grade', 'subjects.course_credit')
      ->get();

    // Definisikan nilai untuk setiap grade
    $gradePoints = [
      GradeType::A->value => 4.00,
      GradeType::A_MIN->value => 3.70,
      GradeType::B->value => 3.00,
      GradeType::B_MIN->value => 2.70,
      GradeType::C->value => 2.00,
      GradeType::C_MIN->value => 1.70,
      GradeType::D->value => 1.00,
      GradeType::E->value => 0.00,
    ];

    $totalQualityPoints = 0;
    $totalCredits = 0;

    foreach ($grades as $grade) :
      $gradePoint = $gradePoints[$grade->grade] ?? 0;
      $credit = $grade->course_credit;
      $qualityPoints = $gradePoint * $credit;
      $totalQualityPoints += $qualityPoints;
      $totalCredits += $credit;
    endforeach;

    // Hitung IPK
    $gpa = $totalCredits > 0 ? $totalQualityPoints / $totalCredits : 0;

    return number_format($gpa, 2);
  }

  public static function log(
    $description,
    $userId = null,
    $logName = 'default',
    $properties = []
  ) {
    $userId = $userId ?? auth()->id();

    // Proses log_name menjadi format yang diinginkan
    $parts = explode('_', strtolower($logName));

    // Ambil action (kata terakhir dari logName)
    $action = end($parts);

    // Ambil subject (kata pertama dari logName, abaikan 'activity')
    $subject = $parts[0]; // Ambil hanya bagian pertama untuk subject

    // Mapping action dan subject ke format yang diinginkan
    $actionMap = ActivityModel::getActionMap(); // Panggil Action Map
    $titleMap = ActivityModel::getTitleMap(); // Panggil Title Map

    // Cari action dan subject di dalam map
    $actionText = $actionMap[$action] ?? Str::title($action);
    $subjectText = $titleMap[$subject] ?? Str::title(str_replace('_', ' ', $subject));

    // Gabungkan action dan subject menjadi log_name yang telah diparsing
    $parsedLogName = "{$actionText} Data {$subjectText}";

    // Simpan activity log dengan log_name yang sudah diparsing
    activity()
      ->causedBy($userId)
      ->withProperties($properties)  // Tambahkan properti tambahan seperti perubahan data
      ->tap(function (Activity $activity) use ($parsedLogName) {
        $activity->log_name = $parsedLogName;
      })
      ->log($description);
  }
}
