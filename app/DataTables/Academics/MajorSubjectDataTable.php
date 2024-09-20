<?php

namespace App\DataTables\Academics;

use App\Helpers\Helper;
use App\Models\MajorSubject;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class MajorSubjectDataTable extends DataTable
{
  public $majorId;

  public function __construct($majorId = null)
  {
    $this->majorId = $majorId;
  }

  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addIndexColumn()
      ->filterColumn('subject_code', function ($query, $keyword) {
        $query->where('subjects.code', 'LIKE', "%{$keyword}%");
      })
      ->filterColumn('subject_name', function ($query, $keyword) {
        $query->where('subjects.name', 'LIKE', "%{$keyword}%");
      })
      ->addColumn('action', 'academics.major_subjects.action')
      ->rawColumns([
        'action'
      ]);
  }

  public function query(MajorSubject $model): QueryBuilder
  {
    if (!$this->majorId) {
      throw new \Exception('Major ID is not set for MajorSubjectDataTable');
    }

    return $model->newQuery()
      ->join('subjects', 'major_subject.subject_id', '=', 'subjects.id')
      ->where('major_subject.major_id', $this->majorId)
      ->select([
        'major_subject.id',
        'major_subject.major_id',
        'major_subject.subject_id',
        'major_subject.semester',
        'subjects.name as subject_name',
        'subjects.code as subject_code'
      ])
      ->orderBy('major_subject.semester');
  }

  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('major-subject-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->addTableClass([
        'table',
        'table-striped',
        'table-bordered',
        'table-hover',
        'table-vcenter',
      ])
      ->orderBy(1)
      ->selectStyleSingle()
      ->processing(true)
      ->retrieve(true)
      ->serverSide(true)
      ->autoWidth(false)
      ->pageLength(5)
      ->responsive(true)
      ->lengthMenu([5, 10, 20, 100])
      ->orderBy(1);
  }

  public function getColumns(): array
  {
    $visibility = Helper::checkPermissions([
      'majors.subjects.destroy',
    ]);

    return [
      Column::make('DT_RowIndex')
        ->title(trans('#'))
        ->orderable(false)
        ->searchable(false)
        ->width('5%')
        ->addClass('text-center'),
      Column::make('subject_code')
        ->title(trans('Kode'))
        ->addClass('text-center'),
      Column::make('subject_name')
        ->title(trans('Matakuliah'))
        ->addClass('text-center'),
      Column::make('semester')
        ->title(trans('Semester'))
        ->addClass('text-center'),
      Column::computed('action')
        ->title(trans('Opsi'))
        ->exportable(false)
        ->printable(false)
        ->visible($visibility)
        ->width('10%')
        ->addClass('text-center'),
    ];
  }

  protected function filename(): string
  {
    return 'MajorSubject_' . date('YmdHis');
  }
}
