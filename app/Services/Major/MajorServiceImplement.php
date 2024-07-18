<?php

namespace App\Services\Major;

use Illuminate\Support\Str;
use App\Imports\MajorImport;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Service;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\Major\MajorRepository;

class MajorServiceImplement extends Service implements MajorService
{
  public function __construct(
    protected MajorRepository $mainRepository
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
      DB::beginTransaction();
      return $this->mainRepository->getQuery();
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
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
      DB::beginTransaction();
      return $this->mainRepository->getWhere(
        wheres: $wheres,
        columns: $columns,
        comparisons: $comparisons,
        orderBy: $orderBy,
        orderByType: $orderByType
      );
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
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
      $this->mainRepository->create($payload);

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
      $this->mainRepository->update($major->id, $payload);

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

      DB::commit();
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
      foreach ($payload['subjects'] as $subject) :
        $subjectsData[] = [
          'uuid' => Str::uuid(),
          'subject_id' => $subject,
          'semester' => $payload['semester'],
          'created_at' => now(),
          'updated_at' => now()
        ];
      endforeach;

      // Add data to table major_subjects
      $major->subjects()->attach($subjectsData);

      // Perbarui total_course_credit
      $major->updateTotalCourseCredit();

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
