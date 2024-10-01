<?php

namespace App\DataTables\Evaluations;

use App\DataTables\Scopes\GlobalFilter;
use App\Helpers\Helper;
use App\Models\Recommendation;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
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
      'subject',
      'subject.grades' => function ($query) {
        $query->where('student_id', $this->studentId);
      }
    ]);

    return (new EloquentDataTable($query))
      ->addIndexColumn()
      ->editColumn('subject_id', fn($row) => $row->subject->name)
      ->addColumn('subject_code', fn($row) => $row->subject->code)
      ->addColumn('course_credit', fn($row) => $row->subject->course_credit)
      ->addColumn('grade', function ($row) {
        // Cek apakah ada nilai untuk mata kuliah tersebut
        return $row->subject->grades->first()->grade ?? '-';
      })
      ->filterColumn('subject_id', function ($query, $keyword) {
        $query->whereHas('subject', function ($query) use ($keyword) {
          $query->where('name', 'LIKE', "%{$keyword}%");
        });
      })
      ->editColumn('note', fn($row) => $row->noteLabel)
      ->addColumn('action', 'evaluations.recommendations.option')
      ->rawColumns([
        'note',
        'action',
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Recommendation $model): QueryBuilder
  {
    $query = $model->newQuery()->latest()->where('student_id', $this->studentId);

    $filterableFields = ['note', 'semester'];

    foreach ($filterableFields as $field) {
      if ($this->request()->has($field)) {
        $filter = new GlobalFilter($this->request());
        $filter->apply($query);
      }
    }

    return $query;
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
      ->ajax([
        'data' => 'function(d) {
          d.note = $("#note").val(); 
          d.semester = $("#semester").val(); 
        }'
      ])
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
      ->lengthMenu([
        [5, 10, 25, -1],
        [5, 10, 25, "All"],
      ])
      ->orderBy(1);
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    // Check Visibility of Action Row
    $visibility = Helper::checkPermissions([
      'recommendations.destroy',
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
      Column::make('subject_id')
        ->title(trans('Matakuliah'))
        ->addClass('text-center'),
      Column::make('course_credit')
        ->title(trans('SKS'))
        ->addClass('text-center'),
      Column::make('grade')
        ->title(trans('Nilai'))
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
      Column::computed('action')
        ->title(trans('Opsi'))
        ->exportable(false)
        ->printable(false)
        ->visible($visibility)
        ->width('5%')
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
