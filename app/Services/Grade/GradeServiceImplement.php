<?php

namespace App\Services\Grade;

use App\Helpers\Helper;
use InvalidArgumentException;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\Enums\GradeType;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use App\Repositories\Grade\GradeRepository;
use App\Helpers\Enums\RecommendationNoteType;
use App\Imports\Grades\GradeImport;
use App\Models\Subject;
use App\Repositories\Major\MajorRepository;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Subject\SubjectRepository;
use App\Repositories\Recommendation\RecommendationRepository;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Str;

class GradeServiceImplement extends Service implements GradeService
{
  public function __construct(
    protected GradeRepository $mainRepository,
    protected SubjectRepository $subjectRepository,
    protected StudentRepository $studentRepository,
    protected RecommendationRepository $recommendationRepository,
    protected MajorRepository $majorRepository
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

      // Find subject
      $subject = $this->subjectRepository->findOrFail($payload['subject_id']);

      // Find Recommendation Data
      $recommendation = $this->recommendationRepository->getWhere(
        wheres: [
          'student_id' => $payload['student_id'],
          'subject_id' => $payload['subject_id'],
        ]
      )->first();

      // Change Note Recommendation
      if ($payload['grade'] == GradeType::E->value) {
        $recommendation->update([
          'note' => RecommendationNoteType::SECOND->value,
        ]);
      } else {
        $recommendation->update([
          'note' => RecommendationNoteType::PASSED->value,
        ]);

        $payload['note'] = "Nilai sudah memenuhi standar kelulusan.";
      }

      // Tambahkan Nilai Mutu Mahasiswa
      $quality = Helper::generateQuality($payload['grade']);

      // Store Data
      $payload['exam_period'] = $recommendation->exam_period;
      $payload['quality'] = $quality;

      $this->mainRepository->create($payload);

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

      // Find Grade Data
      $grade = $this->mainRepository->findOrFail($id);

      // Find Data Recommendation
      $recommendation = $this->recommendationRepository->getWhere(
        wheres: [
          'student_id' => $payload['student_id'],
          'subject_id' => $payload['subject_id'],
        ]
      )->first();

      // Change Note Recommendation
      if ($grade->grade == GradeType::E->value) {
        $recommendation->update([
          'note' => RecommendationNoteType::DONE->value
        ]);
        $payload['note'] = "Perbaikan nilai dari {$grade->grade} menjadi {$payload['grade']}.";
      } else {
        $recommendation->update([
          'note' => RecommendationNoteType::PASSED->value
        ]);
        $payload['note'] = "Nilai sudah memenuhi standar kelulusan";
      }

      // Tambahkan Nilai Mutu Mahasiswa
      $quality = Helper::generateQuality($payload['grade']);
      $payload['quality'] = $quality;

      $this->mainRepository->update($grade->id, $payload);

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

      $studentDetail = Helper::getDataStudent($student->id);

      // Prepare data for the view
      $data = [
        'student' => $student,
        'groupedSubjects' => $groupedSubjects,
        'studentDetail' => $studentDetail
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
    DB::beginTransaction();
    try {
      if ($request->hasFile('file') && $request->file('file')->isValid()) :
        // Get data imports
        $import = new GradeImport();
        Excel::import($import, $request->file('file'));

        $result = [
          'nim' => $import->getStudent()['nim'],
          'student' => $import->getStudent()['student'],
          'major' => $import->getStudent()['major'],
          'subjects' => $import->getSubjects()
        ];

        // Cek apakah NIM sama
        if ((string) $result['nim'] !== $student->nim) {
          $error = "Data <strong>NIM</strong> yang ada pada Excel di <strong>Sheet2</strong> tidak sama dengan <strong>NIM Mahasiswa</strong> dengan nama <strong>{$student->name}</strong> pada halaman ini. Silahkan melakukan pengecekan ulang dan pastikan NIM nya sudah sesuai dengan data mahasiswa yang akan di importkan Nilainya.";
          return redirect(route('grades.show', $student))->with('flashError', $error);
        }

        $major = $this->majorRepository->findOrFail($student->major->id);

        // Ambil semua matakuliah yang ada di jurusan menggunakan tabel pivot
        $majorSubjects = DB::table('subjects')
          ->join('major_subject', 'subjects.id', '=', 'major_subject.subject_id')
          ->where('major_subject.major_id', $major->id)
          ->pluck('subjects.id', 'subjects.code')
          ->toArray();

        // Ambil semua nilai yang sudah ada untuk mahasiswa ini
        $existingGrades = $student->grades()->pluck('subject_id')->toArray();

        $duplicateSubjects = [];
        $newGrades = [];
        $newRecommendations = [];

        // Process grouped data
        foreach ($result['subjects'] as $semester => $courses) {
          foreach ($courses as $course) {
            // Cek apakah matakuliah ada di jurusan mahasiswa
            if (!isset($majorSubjects[$course['code']])) {
              $error = "Matakuliah dengan kode <strong>{$course['code']}</strong> tidak ditemukan dalam daftar matakuliah jurusan <strong>{$major->name}</strong>. Silahkan periksa kembali data yang diimpor.";
              return redirect(route('grades.show', $student))->with('flashError', $error);
            }

            $subjectId = $majorSubjects[$course['code']];

            // Cek apakah matakuliah sudah ada di tabel grades untuk mahasiswa ini
            if (in_array($subjectId, $existingGrades)) {
              $duplicateSubjects[] = $course['code'];
              continue;
            }

            if ($course['grade'] === GradeType::E->value || $course['grade'] === GradeType::D->value) {
              $recommendationNote = RecommendationNoteType::SECOND->value;
              $gradeNote = "Nilai perlu dilakukan direkomendasikan ulang dan diperbaiki";
            } else {
              $recommendationNote = RecommendationNoteType::PASSED->value;
              $gradeNote = "Nilai sudah memenuhi standar kelulusan matakuliah";
            }

            // Tambahkan ke tabel Rekomendasi
            $newRecommendations[] = [
              'uuid' => Str::uuid(),
              'student_id' => $student->id,
              'subject_id' => $subjectId,
              'semester' => $semester,
              'exam_period' => $course['exam_period'],
              'note' => $recommendationNote,
              'created_at' => now(),
              'updated_at' => now(),
            ];

            // Tambahkan ke array untuk penyimpanan batch
            $newGrades[] = [
              'uuid' => Str::uuid(),
              'student_id' => $student->id,
              'subject_id' => $subjectId,
              'grade' => $course['grade'] ?? null,
              'quality' => Helper::generateQuality($course['grade']),
              'mutu' => $course['mutu'] ?? null,
              'exam_period' => $course['exam_period'] ?? null,
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

      endif;
      DB::commit();

      return redirect(route('grades.show', $student))->withSuccess(trans('session.create'));
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
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

      // Find Recommendation Data by student & subject id
      $recommendation = $this->recommendationRepository->getWhere(
        wheres: [
          'student_id' => $grade->student_id,
          'subject_id' => $grade->subject_id,
        ]
      )->first();

      if ($recommendation->note === RecommendationNoteType::PASSED->value || $grade->grade === GradeType::E->value  || $recommendation->note === RecommendationNoteType::DONE->value) :
        $recommendation->update([
          'note' => RecommendationNoteType::FIRST->value
        ]);
      endif;

      $grade->delete();
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
