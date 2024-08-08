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
  public $studentId;

  /**
   * Create a new datatables instance.
   *
   * @return void
   */
  public function __construct(
    $studentId = null
  ) {
    $this->studentId = $studentId;
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
      ->editColumn('subject_id', fn ($row) => $row->subject->name)
      ->addColumn('course_credit', fn ($row) => $row->subject->course_credit)
      ->filterColumn('subject_id', function ($query, $keyword) {
        $query->whereHas('subject', function ($query) use ($keyword) {
          $query->where('name', 'LIKE', "%{$keyword}%");
        });
      })
      ->editColumn('note', fn ($row) => $row->noteLabel)
      ->rawColumns([
        'note',
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Recommendation $model): QueryBuilder
  {
    return $model->newQuery()->where('student_id', $this->studentId);
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
    return [
      Column::make('DT_RowIndex')
        ->title(trans('#'))
        ->orderable(false)
        ->searchable(false)
        ->width('5%')
        ->addClass('text-center'),
      Column::make('subject_id')
        ->title(trans('Matakuliah'))
        ->addClass('text-center'),
      Column::make('course_credit')
        ->title(trans('SKS'))
        ->addClass('text-center'),
      Column::make('semester')
        ->title(trans('Semester'))
        ->addClass('text-center'),
      Column::make('exam_period')
        ->title(trans('Masa Ujian'))
        ->addClass('text-center'),
      Column::make('note')
        ->title(trans('Catatan'))
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
