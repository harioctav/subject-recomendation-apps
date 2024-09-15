<?php

namespace App\Helpers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\Recommendation;
use App\Helpers\Enums\GradeType;
use App\Models\Activity as ActivityModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

  // Log
  // public static function log(
  //   $description,
  //   $userId = null,
  //   $logName = 'default',
  //   $properties = []
  // ) {
  //   $userId = $userId ?? auth()->id();

  //   activity()
  //     ->causedBy($userId)
  //     ->withProperties($properties)  // Tambahkan properti tambahan seperti perubahan data
  //     ->tap(function (Activity $activity) use ($logName) {
  //       $activity->log_name = $logName;
  //     })
  //     ->log($description);
  // }
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

  public static function getDataStudent($stduentId)
  {
    // Student Data
    $student = Student::findOrFail($stduentId);

    // Data matakuliah yang sudah direkomendasikan
    $recommendedSubjects = Recommendation::where('student_id', $student->id)->pluck('subject_id');

    // Ambil ID mata kuliah yang dinilai dengan nilai bukan 'E'
    $passedSubjects = Grade::where('student_id', $student->id)
      ->whereIn('subject_id', $recommendedSubjects)
      ->where('grade', '!=', GradeType::E->value);

    // Hitung total SKS dari mata kuliah yang lulus berdasarkan exam_period
    $examPeriod55555 = $passedSubjects->clone()->where('exam_period', '55555')->pluck('subject_id');
    $totalCourseCredit55555 = Subject::whereIn('id', $examPeriod55555)->sum('course_credit');

    $examPeriodByCuriculum = $passedSubjects->clone()->where('exam_period', '!=', '55555')->pluck('subject_id');
    $totalCourseCreditByCuriculum = Subject::whereIn('id', $examPeriodByCuriculum)->sum('course_credit');

    // Hitung total SKS dari mata kuliah yang lulus
    $totalCompletedCourseCredit = Subject::whereIn('id', $passedSubjects->pluck('subject_id'))->sum('course_credit');
    $totalCourseCredit = $student->major->total_course_credit;

    // IPK
    $gpa = Helper::calculateGPA($student->id);
    $mutu = $passedSubjects->sum('mutu');

    $studentData = [
      'total_compeleted_55555' => $totalCourseCredit55555,
      'total_compeleted_by_curiculum' => $totalCourseCreditByCuriculum,
      'total_compeleted_course_credit' => $totalCompletedCourseCredit,
      'total_course_credit' => $totalCourseCredit,
      'gpa' => $gpa,
      'mutu' => rtrim(rtrim(number_format($mutu, 2), '0'), '.'),
    ];

    return $studentData;
  }
}
