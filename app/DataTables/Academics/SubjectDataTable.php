<?php

namespace App\DataTables\Academics;

use App\Helpers\Helper;
use App\Models\Subject;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use App\Services\Subject\SubjectService;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class SubjectDataTable extends DataTable
{
  /**
   * Create a new datatables instance.
   *
   * @return void
   */
  public function __construct(
    protected SubjectService $subjectService,
  ) {
    // 
  }

  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addIndexColumn()
      ->addColumn('exam_time', fn ($row) => $row->exam_time ?: '--')
      ->addColumn('course_credit', fn ($row) => $row->course_credit ?: '--')
      ->addColumn('action', 'academics.subjects.action')
      ->rawColumns([
        'action'
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Subject $model): QueryBuilder
  {
    return $this->subjectService->getQuery()->oldest('code');
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('subject-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      //->dom('Bfrtip')
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
      ->lengthMenu([5, 10, 20])
      ->orderBy(1);
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    // Check Visibility of Action Row
    $visibility = Helper::checkPermissions([
      'subjects.edit',
      'subjects.destroy',
    ]);

    return [
      Column::make('DT_RowIndex')
        ->title(trans('#'))
        ->orderable(false)
        ->searchable(false)
        ->width('5%')
        ->addClass('text-center'),
      Column::make('code')
        ->title(trans('Kode'))
        ->addClass('text-center'),
      Column::make('name')
        ->title(trans('Nama'))
        ->addClass('text-center'),
      Column::make('course_credit')
        ->title(trans('SKS'))
        ->addClass('text-center'),
      Column::make('exam_time')
        ->title(trans('Waktu Ujian'))
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

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Subject_' . date('YmdHis');
  }
}
