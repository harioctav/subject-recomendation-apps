<?php

namespace App\Services\Student;

use App\Helpers\Enums\GradeType;
use App\Helpers\Enums\RecommendationStatusType;
use App\Helpers\Enums\SubjectNoteType;
use App\Helpers\Helper;
use App\Imports\StudentImport;
use App\Models\Grade;
use App\Models\Subject;
use App\Repositories\Grade\GradeRepository;
use App\Repositories\Recommendation\RecommendationRepository;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Village\VillageRepository;
use Maatwebsite\Excel\Facades\Excel;

class StudentServiceImplement extends Service implements StudentService
{
  public function __construct(
    protected StudentRepository $mainRepository,
    protected VillageRepository $villageRepository,
    protected RecommendationRepository $recommendationRepository,
    protected GradeRepository $gradeRepository
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
   * Get data in model Permission Category with relations
   *
   */
  public function with(array $with = [])
  {
    try {
      return $this->mainRepository->with($with);
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

  /**
   * Get all student data based on the provided request.
   */
  public function getStudentAllData($request)
  {
    try {
      $payload = $request->validated();

      // Get student data
      $student = $this->mainRepository->getWhere(
        wheres: [
          'nim' => $payload['nim']
        ]
      )->first();

      if (empty($student)) {
        return back()->with('error', trans('session.students.nim.not-found', ['nim' => $payload['nim']]));
      }

      $student->with([
        'grades',
        'major.subjects',
      ]);

      $subjects = $student->major->subjects->mapToGroups(function ($subject) use ($student) {
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

      $detail = $this->getStudentAcademicInfo($student->id);

      return view('academics.students.data', compact('student', 'subjects', 'detail'));
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function getStudentDataWithRecommendations($student)
  {
    $studentData = [
      'nim' => $student->nim,
      'major' => $student->major->name
    ];

    $existingGrades = $student->grades->pluck('subject_id')->toArray();

    $recommendedSubjects = $student->recommendations
      ->groupBy('semester')
      ->map(function ($recommendations, $semester) use ($existingGrades) {
        $subjects = $recommendations
          ->reject(function ($recommendation) use ($existingGrades) {
            return in_array($recommendation->subject_id, $existingGrades);
          })
          ->map(function ($recommendation) {
            return [
              'id' => $recommendation->subject->id,
              'code' => $recommendation->subject->code,
              'name' => $recommendation->subject->name,
              'exam_period' => $recommendation->exam_period
            ];
          });

        return [
          'semester' => $semester,
          'subjects' => $subjects->values()
        ];
      })
      ->filter(function ($semesterData) {
        return $semesterData['subjects']->isNotEmpty();
      })
      ->values();

    return [
      'student' => $studentData,
      'subjects' => $recommendedSubjects
    ];
  }

  public function getStudentAcademicInfo($id)
  {
    // 1. Data mahasiswa
    $student = $this->mainRepository->findOrFail($id);

    $allSubjects = $student->major->subjects;
    $totalCourseCredit = $this->countTotalCourseCredit($allSubjects);

    // Filter Rekomendasi
    $statusFilters = [
      'passed' => [
        RecommendationStatusType::LULUS->value,
        RecommendationStatusType::SUDAH_DIPERBAIKI->value,
      ],
      'ongoing' => [
        RecommendationStatusType::DIREKOMENDASIKAN->value,
        RecommendationStatusType::SEMESTER_BERJALAN->value,
      ],
      'improvement' => [
        RecommendationStatusType::PERLU_PERBAIKAN->value,
      ],
    ];

    // Menghitung informasi kredit dan nilai mutu
    $creditInfo = $this->calculateCreditInfo($student->id, $statusFilters);

    // Perhitungan IPK
    $gpa = Helper::calculateGPA($student->id);
    $percentage = ($gpa / 4) * 100;

    // Menghitung SKS yang benar-benar sudah lulus (tidak termasuk 'Perlu Perbaikan')
    $actualPassedCredits = $creditInfo['passed'];

    // Menghitung estimasi sisa semester
    $remainingSemesters = $this->estimateRemainingSemesters($actualPassedCredits, $totalCourseCredit);

    // Get recommended subjects for the student
    $recommendationByStudentId = $this->recommendationRepository->getWhere(
      wheres: [
        'student_id' => $student->id
      ]
    )->pluck('subject_id');

    $gradeByStudentId = $this->gradeRepository->getWhere(
      wheres: [
        'student_id' => $student->id
      ]
    );

    $mutu = $gradeByStudentId->whereIn('subject_id', $recommendationByStudentId)
      ->where('grade', '!=', GradeType::E->value)
      ->sum('mutu');

    $hasGradeE = $gradeByStudentId->where('grade', GradeType::E->value)
      ->exists();

    return [
      'student' => $student,
      'credit_has_been_taken' => $creditInfo['total'], // total SKS yang sudah ditempuh atau di ambil
      'credit_has_been_passed' => $creditInfo['passed'], // Total SKS yang sudah lulus
      'credit_being_taken' => $creditInfo['ongoing'], // Total SKS yang sedang diambil
      'credit_need_improvement' => $creditInfo['improvement'], // Total SKS yang perlu perbaikan atau dalam perbaikan
      'transfer_credit' => $creditInfo['transfer'], // alih kredit
      'credit_by_curriculum' => $creditInfo['curriculum'], // berdasarkan kurikulum
      'total_credit_not_yet_taken' => $totalCourseCredit - $creditInfo['total'], // total sks yang belum di tempuh
      'total_credit_not_yet_taken_by_passed' => $totalCourseCredit - $creditInfo['passed'], // total sks yang belum ditempuh berdasarkan kelulusan
      'total_course_credit' => $totalCourseCredit, // total sks wajib tempuh
      'gpa' => $gpa,
      'percentage' => $percentage,
      'estimated_remaining_semesters' => $remainingSemesters,
      'has_grade_e' => $hasGradeE,
      'mutu' => rtrim(rtrim(number_format($mutu, 2), '0'), '.'), // Total nilai mutu
    ];
  }

  private function calculateCreditInfo($studentId, $statusFilters)
  {
    $query = $this->recommendationRepository->getQuery()
      ->where('student_id', $studentId)
      ->join('subjects', 'recommendations.subject_id', '=', 'subjects.id')
      ->select('recommendations.note', 'recommendations.exam_period', 'subjects.course_credit');

    $results = $query->get();

    $creditInfo = [
      'total' => 0,
      'passed' => 0,
      'ongoing' => 0,
      'improvement' => 0,
      'transfer' => 0,
      'curriculum' => 0,
    ];

    foreach ($results as $result) {
      $credit = $result->course_credit;
      $creditInfo['total'] += $credit;

      if ($result->exam_period == '55555') {
        $creditInfo['transfer'] += $credit;
      } else {
        $creditInfo['curriculum'] += $credit;
      }

      foreach ($statusFilters as $key => $statuses) {
        if (in_array($result->note, $statuses)) {
          $creditInfo[$key] += $credit;
          break;
        }
      }
    }

    // Tidak perlu mengurangi $creditInfo['improvement'] dari $creditInfo['passed']

    return $creditInfo;
  }

  private function estimateRemainingSemesters($passedCredits, $totalCredits)
  {
    $remainingCredits = $totalCredits - $passedCredits;
    if ($remainingCredits <= 0) {
      return 0;
    }

    $semesters = 0;
    $creditsPerSemester = [20, 20]; // Semester 1 dan 2

    while ($remainingCredits > 0) {
      $semesters++;
      $maxCredits = ($semesters <= 2) ? $creditsPerSemester[$semesters - 1] : 24;
      $remainingCredits -= min($remainingCredits, $maxCredits);
    }

    return $semesters;
  }

  private function countTotalCourseCredit($subjects)
  {
    $totalCourseCredit = 0;
    $subjectsBySemester = $subjects->groupBy('pivot.semester');

    foreach ($subjectsBySemester as $semester => $subjects) {
      // Pisahkan mata kuliah berdasarkan "PILIH SALAH SATU"
      $withPilihSalahSatu = $subjects->filter(function ($subject) {
        return str_contains($subject->note, SubjectNoteType::PS->value);
      });

      $withoutPilihSalahSatu = $subjects->filter(function ($subject) {
        return !str_contains($subject->note, SubjectNoteType::PS->value);
      });

      // Tambahkan total SKS dari mata kuliah tanpa "PILIH SALAH SATU"
      foreach ($withoutPilihSalahSatu as $subject) {
        $totalCourseCredit += $subject->course_credit; // Mengambil SKS dari kolom course_credit di tabel subjects
      }

      // Jika ada mata kuliah "PILIH SALAH SATU", hanya tambahkan salah satu dari grup ini
      if ($withPilihSalahSatu->isNotEmpty()) {
        $totalCourseCredit += $withPilihSalahSatu->max()->course_credit; // Ambil salah satu SKS dari mata kuliah pilihan
        // $totalCourseCredit += $withPilihSalahSatu->first()->course_credit; // Ambil salah satu SKS dari mata kuliah pilihan
      }
    }

    return $totalCourseCredit;
  }

  public function handleStoreData($request)
  {
    try {
      DB::beginTransaction();

      // Fetch request form
      $payload = $request->validated();

      if (!empty($payload['village'])) {
        $village = $this->villageRepository->findOrFail($payload['village']);
        $payload['village_id'] = $village->id;
      } else {
        $payload['village_id'] = null;
      }

      // handle file upload
      $avatar = Helper::uploadFile(
        $request,
        "images/students"
      );

      // Save data
      $payload['avatar'] = $avatar;
      $payload['village_id'] = $village->id;
      $payload['birth_place'] = strtoupper($payload['birth_place']);
      $payload['parent_name'] = strtoupper($payload['parent_name']);

      // Menghilangkan province, regency, district, dan post_code menggunakan array_except()
      $create = Arr::except($payload, ['province', 'regency', 'district', 'village', 'post_code']);

      $student = $this->mainRepository->create($create);

      // Activity Log
      Helper::log(
        trans('activity.students.create', ['student' => $student->name]),
        me()->id,
        'student_activity_store',
        [
          'data' => $student
        ]
      );

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleUpdateData($request, $id)
  {
    try {
      DB::beginTransaction();

      // Fetch request form
      $payload = $request->validated();

      $student = $this->mainRepository->findOrFail($id);
      $avatar = Helper::uploadFile($request, "images/students", $student->avatar);

      if (!empty($payload['village'])) {
        $village = $this->villageRepository->findOrFail($payload['village']);
        $payload['village_id'] = $village->id;
      } else {
        $payload['village_id'] = optional($student->village)->id;
      }

      $payload['avatar'] = $avatar;
      $payload['birth_place'] = strtoupper($payload['birth_place']);
      $payload['parent_name'] = strtoupper($payload['parent_name']);

      // Menghilangkan province, regency, district, dan post_code menggunakan array_except()
      $update = Arr::except($payload, ['province', 'regency', 'district', 'village', 'post_code']);

      // update database
      $student->update($update);

      // Activity Log
      Helper::log(
        trans('activity.students.edit', [
          'student' => $student->name
        ]),
        me()->id,
        'student_activity_update',
        [
          'data' => $student
        ]
      );

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  /**
   * Delete data in database & delete image (if not null) in storage.
   */
  public function handleDeleteData($id)
  {
    try {
      DB::beginTransaction();

      // Handle delete
      $student = $this->mainRepository->findOrFail($id);

      // Activity Log
      Helper::log(
        trans('activity.students.destroy', [
          'student' => $student->name
        ]),
        me()->id,
        'student_activity_destroy',
        [
          'data' => $student
        ]
      );

      $student->delete();

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleImportData($request)
  {
    $payload = $request->validated();

    $file = $payload['file'];
    $import = new StudentImport();
    Excel::import($import, $file);

    $errors = $import->getErrors();
    $importedCount = $import->getImportedCount();
    $skippedCount = $import->getSkippedCount();

    Helper::log(
      trans('activity.students.import'),
      me()->id,
      'student_activity_import'
    );

    // Cek jika terdapat error yang valid
    if (!empty($errors)) {
      return redirect()->back()->withErrors($errors)->with([
        'warning' => 'Import selesai dengan beberapa peringatan.',
        'imported' => $importedCount,
        'skipped' => $skippedCount
      ]);
    }

    // Jika tidak ada error, kembalikan pesan sukses
    return redirect()->back()->with([
      'success' => trans('session.create'),
      'imported' => $importedCount,
      'skipped' => $skippedCount
    ]);
  }

  public function handleRestoreData($id)
  {
    try {
      $student = $this->mainRepository->getTrashed($id);
      // Activity Log
      Helper::log(
        trans('activity.students.restore', [
          'student' => $student->name
        ]),
        me()->id,
        'student_activity_restore',
        [
          'data' => $student
        ]
      );
      return $this->mainRepository->handleRestoreData($id);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleForceDeleteData($id)
  {
    try {
      DB::beginTransaction();

      // Find Student
      $student = $this->mainRepository->getTrashed($id);

      // Activity Log
      Helper::log(
        trans('activity.students.delete', [
          'student' => $student->name
        ]),
        me()->id,
        'student_activity_delete',
        [
          'data' => $student
        ]
      );

      if ($student->avatar) :
        Storage::delete($student->avatar);
      endif;

      // Force Delete
      $this->mainRepository->handleForceDeleteData($student->id);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }
}
