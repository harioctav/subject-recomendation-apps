<?php

namespace App\DataTables\Evaluations;

use App\Helpers\Helper;
use App\Models\Recommendation;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use App\Services\Recommendation\RecommendationService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class RecommendationDataTable extends DataTable
{
  /**
   * Create a new datatables instance.
   *
   * @return void
   */
  public function __construct(
    protected RecommendationService $recommendationService,
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
    $query = $query->with([
      'student',
      'subject'
    ]);

    return (new EloquentDataTable($query))
      ->addIndexColumn()
      ->editColumn('student_id', fn ($row) => $row->student->name)
      ->editColumn('subject_id', fn ($row) => $row->subject->name)
      ->filterColumn('student_id', function ($query, $keyword) {
        $query->whereHas('student', function ($query) use ($keyword) {
          $query->where('name', 'LIKE', "%{$keyword}%");
        });
      })
      ->filterColumn('subject_id', function ($query, $keyword) {
        $query->whereHas('subject', function ($query) use ($keyword) {
          $query->where('name', 'LIKE', "%{$keyword}%");
        });
      })
      ->addColumn('action', 'evaluations.recommendations.action')
      ->rawColumns([
        'action',
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Recommendation $model): QueryBuilder
  {
    return $this->recommendationService->getQuery()->latest();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('recommendation-table')
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
      'recommendations.edit',
      'recommendations.destroy',
    ]);

    return [
      Column::make('DT_RowIndex')
        ->title(trans('#'))
        ->orderable(false)
        ->searchable(false)
        ->width('5%')
        ->addClass('text-center'),
      Column::make('student_id')
        ->title(trans('Mahasiswa'))
        ->addClass('text-center'),
      Column::make('subject_id')
        ->title(trans('Matakuliah'))
        ->addClass('text-center'),
      Column::make('semester')
        ->title(trans('Semester'))
        ->addClass('text-center'),
      Column::computed('action')
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
    return 'Recommendation_' . date('YmdHis');
  }
}
