<?php

namespace App\Services\Grade;

use App\Helpers\Helper;
use App\Imports\GradesImport;
use InvalidArgumentException;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\Enums\GradeType;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use App\Repositories\Grade\GradeRepository;
use App\Helpers\Enums\RecommendationStatusType;
use App\Repositories\Major\MajorRepository;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Subject\SubjectRepository;
use App\Repositories\Recommendation\RecommendationRepository;
use App\Services\Student\StudentService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class GradeServiceImplement extends Service implements GradeService
{
  public function __construct(
    protected GradeRepository $mainRepository,
    protected SubjectRepository $subjectRepository,
    protected StudentRepository $studentRepository,
    protected RecommendationRepository $recommendationRepository,
    protected MajorRepository $majorRepository,
    protected StudentService $studentService
  ) {
    // 
  }

  /**
   * Return query for model Role
   *
   */
  public function getQuery()
  {
    try {
      return $this->mainRepository->getQuery();
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Get data by row name use where or where in function
   *
   */
  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  ) {
    try {
      return $this->mainRepository->getWhere(
        wheres: $wheres,
        columns: $columns,
        comparisons: $comparisons,
        orderBy: $orderBy,
        orderByType: $orderByType
      );
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleStoreData($request)
  {
    try {
      DB::beginTransaction();

      // Fetch Data
      $payload = $request->validated();

      foreach ($payload['subjects'] as $subjectId) {
        // Find subject
        $subject = $this->subjectRepository->findOrFail($subjectId);

        // Find Recommendation Data
        $recommendation = $this->recommendationRepository->getWhere(
          wheres: [
            'student_id' => $payload['student_id'],
            'subject_id' => $subjectId,
          ]
        )->first();

        // Change Note Recommendation
        if ($payload['grade'] == GradeType::E->value) {
          $recommendation->update([
            'note' => RecommendationStatusType::PERLU_PERBAIKAN->value,
          ]);
          $note = "Nilai perlu perbaikan.";
        } else {
          $recommendation->update([
            'note' => RecommendationStatusType::LULUS->value,
          ]);
          $note = "Nilai sudah memenuhi standar kelulusan.";
        }

        // Tambahkan Nilai Mutu Mahasiswa
        $quality = Helper::generateQuality($payload['grade']);

        // Store Data
        $gradeData = [
          'student_id' => $payload['student_id'],
          'subject_id' => $subjectId,
          'grade' => $payload['grade'],
          'mutu' => $payload['mutu'],
          'exam_period' => $recommendation->exam_period,
          'quality' => $quality,
          'note' => $note,
        ];

        $grade = $this->mainRepository->create($gradeData);

        // Activity Log
        Helper::log(
          trans('activity.grades.create', [
            'grade' => $subject->name
          ]),
          me()->id,
          'grade_activity_store',
          [
            'subject' => $subject,
            'data' => $grade,
          ]
        );
      }

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Updates a grade record in the system.
   *
   * @param \Illuminate\Http\Request $request
   * @param int $id The ID of the grade record to update.
   * @return void
   */
  public function handleUpdateData($request, $id)
  {
    try {
      DB::beginTransaction();
      $payload = $request->validated();

      foreach ($payload['subjects'] as $subjectId) {
        // Find Grade Data
        $grade = $this->mainRepository->findOrFail($id);
        $subject = $this->subjectRepository->findOrFail($grade->subject_id);

        // Find Data Recommendation
        $recommendation = $this->recommendationRepository->getWhere(
          wheres: [
            'student_id' => $payload['student_id'],
            'subject_id' => $subjectId,
          ]
        )->first();

        // Change Note Recommendation
        if ($grade->grade == GradeType::E->value) {
          $recommendation->update([
            'note' => RecommendationStatusType::SUDAH_DIPERBAIKI->value
          ]);
          $payload['note'] = "Perbaikan nilai dari {$grade->grade} menjadi {$payload['grade']}.";
        } else if ($grade->grade !== GradeType::E->value && $recommendation->note === RecommendationStatusType::REQUEST_PERBAIKAN->value) {
          $recommendation->update([
            'note' => RecommendationStatusType::SUDAH_DIPERBAIKI->value
          ]);
          $payload['note'] = "Perbaikan nilai dari {$grade->grade} menjadi {$payload['grade']}.";
        } else {
          $recommendation->update([
            'note' => RecommendationStatusType::LULUS->value
          ]);
          $payload['note'] = "Nilai sudah memenuhi standar kelulusan";
        }

        // Tambahkan Nilai Mutu Mahasiswa
        $quality = Helper::generateQuality($payload['grade']);
        $payload['quality'] = $quality;

        $grade->update($payload);

        // Activity Log
        Helper::log(
          trans('activity.grades.edit', [
            'grade' => $subject->name
          ]),
          me()->id,
          'grade_activity_update',
          [
            'subject' => $subject,
            'data' => $grade,
          ]
        );
      }

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Handles the export of student transcript data.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function handleExportData($student)
  {
    try {
      $student->with([
        'grades',
        'major.subjects',
      ]);

      // Group subjects by semester and check if they have grades
      $groupedSubjects = $student->major->subjects->mapToGroups(function ($subject) use ($student) {
        $grade = $student->grades->firstWhere('subject_id', $subject->id);
        $semester = $subject->pivot->semester;

        $subject->course_credit = ($subject->course_credit === '') ? 0 : $subject->course_credit;

        return [$semester => [
          'subject' => $subject,
          'has_grade' => !is_null($grade),
          'grade' => $grade,
          'mutu' => $grade ? $grade->mutuLabel : null,
          'exam_period' => $grade ? $grade->exam_period : null,
        ]];
      });

      $formattedDate = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
      $fileTitle = "{$formattedDate}-{$student->name}-TRANSCRIPT-SEMENTARA.pdf";

      $detail = $this->studentService->getStudentAcademicInfo($student->id);

      // Prepare data for the view
      $data = [
        'groupedSubjects' => $groupedSubjects,
        'detail' => $detail
      ];

      // Generate PDF
      $pdf = Pdf::loadView('exports.transcript', $data);
      return $pdf->stream($fileTitle);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleImportData($student, $request)
  {
    // get file
    if ($request->hasFile('file') && $request->file('file')->isValid()) {
      $import = new GradesImport;
      Excel::import($import, $request->file('file'));

      $errors = $import->getErrors();
      $result = $this->getResult($import);

      // Find major
      $major = $this->majorRepository->findOrFail($student->major->id);

      // cek apakah nim dan jurusannya sama atau tidak
      if ($result['nim'] !== $student->nim || strtolower($result['major']) !== strtolower($major->name)) {
        return back()->with('flashError', "Nim atau Program Studi tidak sama dengan data {$student->name}.");
      }

      // Ambil semua matakuliah yang ada di jurusan menggunakan tabel pivot
      $majorSubjects = $major->subjects->pluck('id', 'code')->toArray();

      // Ambil semua nilai yang sudah ada untuk mahasiswa ini
      $existingGrades = $student->grades()->pluck('subject_id')->toArray();

      $duplicateSubjects = [];
      $newGrades = [];
      $newRecommendations = [];

      foreach ($result['subjects'] as $semester => $courses) {
        foreach ($courses as $course) {

          $code = trim($course['kode_matakuliah']);
          $grade = trim($course['nilai']);
          $examPeriod = trim($course['masa_ujian']);

          // Cek apakah matakuliah ada di jurusan mahasiswa
          if (!isset($majorSubjects[$code])) {
            $error = "Matakuliah dengan kode <strong>{$code}</strong> tidak ditemukan dalam daftar matakuliah jurusan <strong>{$major->name}</strong>. Silahkan periksa kembali data yang diimpor.";
            return redirect(route('grades.show', $student))->with('flashError', $error);
          }

          $subjectId = $majorSubjects[$code];

          // Cek apakah matakuliah sudah ada di tabel grades untuk mahasiswa ini
          if (in_array($subjectId, $existingGrades)) {
            $duplicateSubjects[] = $code;
            continue;
          }

          if ($grade === GradeType::E->value) {
            $recommendationNote = RecommendationStatusType::PERLU_PERBAIKAN->value;
            $gradeNote = "Nilai perlu dilakukan direkomendasikan ulang dan diperbaiki";
          } else {
            $recommendationNote = RecommendationStatusType::LULUS->value;
            $gradeNote = "Nilai sudah memenuhi standar kelulusan matakuliah";
          }

          // Tambahkan ke tabel Rekomendasi
          $newRecommendations[] = [
            'uuid' => Str::uuid(),
            'student_id' => $student->id,
            'subject_id' => $subjectId,
            'semester' => $semester,
            'exam_period' => $examPeriod,
            'note' => $recommendationNote,
            'created_at' => now(),
            'updated_at' => now(),
          ];

          // Tambahkan ke array untuk penyimpanan batch
          $newGrades[] = [
            'uuid' => Str::uuid(),
            'student_id' => $student->id,
            'subject_id' => $subjectId,
            'grade' => $grade ?? null,
            'quality' => Helper::generateQuality($grade),
            'mutu' => $course['nilai_mutu'] ?? null,
            'exam_period' => $examPeriod ?? null,
            'note' => $gradeNote,
            'created_at' => now(),
            'updated_at' => now(),
          ];
        }
      }

      // Simpan nilai baru secara batch
      if (!empty($newGrades)) {
        DB::table('recommendations')->insert($newRecommendations);
        DB::table('grades')->insert($newGrades);
      }

      if (!empty($duplicateSubjects)) {
        $duplicateList = implode(', ', $duplicateSubjects);
        $warningMessage = "Beberapa matakuliah sudah ada dan tidak diimpor ulang: <strong>{$duplicateList}</strong>. Matakuliah lainnya berhasil diimpor.";
        DB::commit();
        return redirect(route('grades.show', $student))->with('flashError', $warningMessage);
      }
    }

    // Activity Log
    Helper::log(
      trans('activity.grades.import'),
      me()->id,
      'grade_activity_import'
    );

    DB::commit();

    return redirect(route('grades.show', $student))->withSuccess(trans('session.create'));
  }

  protected function getResult($import)
  {
    return [
      'errors' => $import->getErrors(),
      'nim' => $import->getNim(),
      'major' => $import->getMajor(),
      'subjects' => $import->getCourses()
    ];
  }

  /**
   * Handles the deletion of a grade.
   *
   * @param int $id The ID of the grade to be deleted.
   * @return void
   */
  public function handleDestroyData(int $id)
  {
    try {
      // Find Grade Data
      $grade = $this->mainRepository->findOrFail($id);
      $subject = $this->subjectRepository->findOrFail($grade->subject_id);

      // Find Recommendation Data by student & subject id
      $recommendation = $this->recommendationRepository->getWhere(
        wheres: [
          'student_id' => $grade->student_id,
          'subject_id' => $grade->subject_id,
        ]
      )->first();

      if ($recommendation->note === RecommendationStatusType::LULUS->value || $grade->grade === GradeType::E->value  || $recommendation->note === RecommendationStatusType::SUDAH_DIPERBAIKI->value) :
        $recommendation->update([
          'note' => RecommendationStatusType::DIREKOMENDASIKAN->value
        ]);
      endif;

      // Activity Log
      Helper::log(
        trans('activity.grades.destroy', [
          'grade' => $subject->name
        ]),
        me()->id,
        'grade_activity_destroy',
        [
          'subject' => $subject,
          'data' => $grade,
        ]
      );

      $grade->delete();
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
