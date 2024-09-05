<?php

namespace App\Services\Grade;

use App\Models\Grade;
use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Subject;
use InvalidArgumentException;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\Enums\GradeType;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use App\Repositories\Grade\GradeRepository;
use App\Helpers\Enums\RecommendationNoteType;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Subject\SubjectRepository;
use App\Repositories\Recommendation\RecommendationRepository;

class GradeServiceImplement extends Service implements GradeService
{
  public function __construct(
    protected GradeRepository $mainRepository,
    protected SubjectRepository $subjectRepository,
    protected StudentRepository $studentRepository,
    protected RecommendationRepository $recommendationRepository
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

        $recommendation->where('note', RecommendationNoteType::REPAIR->value)->delete();

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
  public function handleExportData($request)
  {
    try {
      // Get Request
      $payload = $request->validated();

      // Get Student with related data
      $student = Student::with(['major.subjects', 'grades'])->findOrFail($payload['student_id']);

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

      if ($recommendation->note === RecommendationNoteType::PASSED->value || $grade->grade === GradeType::E->value) :
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
