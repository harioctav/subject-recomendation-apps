<?php

namespace App\Services\Major;

use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Imports\MajorImport;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\Major\MajorRepository;
use App\Repositories\Subject\SubjectRepository;

class MajorServiceImplement extends Service implements MajorService
{
  public function __construct(
    protected MajorRepository $mainRepository,
    protected SubjectRepository $subjectRepository
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

      // Fetch request data
      $payload = $request->validated();

      // Save data to database
      $major = $this->mainRepository->create($payload);

      // Activity Log
      Helper::log(
        trans('activity.majors.create', ['major' => $major->name]),
        me()->id,
        'major_activity_store',
        [
          'data' => $major
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

      // Fetch request data
      $payload = $request->validated();

      // Find data
      $major = $this->mainRepository->findOrFail($id);

      // Update data in database
      $major->update($payload);

      // Activity Log
      Helper::log(
        trans('activity.majors.edit', ['major' => $major->name]),
        me()->id,
        'major_activity_update',
        [
          'data' => $major,
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
   * Import data to database
   * 
   */
  public function handleImportData($request)
  {
    try {
      DB::beginTransaction();

      // Fetch request data
      $payload = $request->validated();

      // Save data to database
      Excel::import(new MajorImport, $payload['file']);

      Helper::log(
        trans('activity.majors.import'),
        me()->id,
        'major_activity_import'
      );

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleDestroyData(int $id)
  {
    try {
      DB::beginTransaction();

      // Find data
      $major = $this->mainRepository->findOrFail($id);

      if (isset($major->students)) :
        return response()->json([
          'message' => trans('session.delete_error')
        ], 400);
      endif;

      // Activity Log
      Helper::log(
        trans('activity.majors.destroy', ['major' => $major->name]),
        me()->id,
        'major_activity_destroy',
        [
          'data' => $major,
        ]
      );

      $this->mainRepository->delete($major->id);


      DB::commit();

      return response()->json([
        'message' => trans('session.delete'),
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleStoreSubjectToMajorData($request, $major)
  {
    try {
      DB::beginTransaction();

      // Fetch request data
      $payload = $request->validated();

      $subjectsData = [];
      $subjectNames = []; // Array untuk menyimpan nama-nama subject
      foreach ($payload['subjects'] as $subject) {
        $subjectModel = $this->subjectRepository->findOrFail($subject);
        $subjectsData[] = [
          'uuid' => Str::uuid(),
          'subject_id' => $subject,
          'semester' => $payload['semester'],
          'created_at' => now(),
          'updated_at' => now()
        ];
        $subjectNames[$subject] = $subjectModel->name; // Menyimpan nama subject dengan key subject_id
      }

      // Add data to table major_subjects
      $major->subjects()->attach($subjectsData);

      // Perbarui total_course_credit
      $major->updateTotalCourseCredit();

      // Activity Log
      Helper::log(
        trans('activity.majors.subjects.create', [
          'major' => $major->name,
          'subject' => implode(', ', $subjectNames), // Menggunakan nama-nama subject
        ]),
        me()->id,
        'major_subject_activity_store',
        [
          'major' => [
            'id' => $major->id,
            'name' => $major->name,
          ],
          'subjects' => array_map(function ($item) use ($subjectNames) {
            return [
              'id' => $item['subject_id'],
              'name' => $subjectNames[$item['subject_id']] ?? 'Unknown',
              'semester' => $item['semester'],
            ];
          }, $subjectsData),
        ]
      );

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException(trans('session.log.error'));
    }
  }

  public function handleDestroySubjectToMajorData($major, $subject)
  {
    try {
      DB::beginTransaction();

      // activity log
      Helper::log(
        trans('activity.majors.subjects.destroy', [
          'subject' => $subject->name,
          'major' => $major->name
        ]),
        me()->id,
        'major_subject_activity_destroy',
        [
          'data' => $subject
        ]
      );

      // Hapus relasi dari tabel major_subject
      $deleted = DB::table('major_subject')
        ->where('major_id', $major->id)
        ->where('subject_id', $subject->id)
        ->delete();

      if (!$deleted) {
        throw new \Exception('Gagal menghapus mata kuliah dari program studi.');
      }

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => 'Mata kuliah berhasil dihapus dari program studi.'
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error deleting major subject: ' . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menghapus mata kuliah dari program studi.'
      ], 500);
    }
  }
}
