<?php

namespace App\DataTables\Evaluations;

use App\DataTables\Scopes\GlobalFilter;
use App\Helpers\Helper;
use App\Models\Student;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class StudentDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    $query = $query->with('major');

    return (new EloquentDataTable($query))
      ->addIndexColumn()
      ->editColumn('major_id', fn($row) => $row->major->name)
      ->editColumn('email', fn($row) => $row->email ?? "--")
      ->editColumn('status', fn($row) => $row->statusLabel)
      ->editColumn('student_status', fn($row) => $row->studentStatusLabel)
      ->addColumn('recommendations', fn($row) => $row->recommendations->count() . ' Data')
      ->addColumn('grades', fn($row) => $row->grades->count())
      ->filterColumn('major_id', function ($query, $keyword) {
        $query->whereHas('major', function ($query) use ($keyword) {
          $query->where('name', 'LIKE', "%{$keyword}%");
        });
      })
      ->addColumn('action', function ($row) {
        if (request()->routeIs('grades.*')):
          return view('evaluations.grades.action', compact('row'));
        elseif (request()->routeIs('recommendations.*')):
          return view('evaluations.recommendations.action', compact('row'));
        endif;
      })
      ->rawColumns([
        'action',
        'status',
        'student_status'
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Student $model): QueryBuilder
  {
    $query = $model->newQuery()->orderBy('name', 'asc')->latest();

    if ($this->request()->has('status')) {
      $statusFilter = new GlobalFilter($this->request());
      $statusFilter->apply($query);
    }

    return $query;
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('student-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      //->dom('Bfrtip')
      ->ajax([
        'data' => 'function(d) { d.status = $("#status").val(); }'
      ])
      ->addTableClass([
        'table',
        'table-striped',
        'table-bordered',
        'table-hover',
        'table-vcenter',
      ])
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

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    // Check Visibility of Action Row
    $visibility = Helper::checkPermissions([
      'recommendations.create',
      'recommendations.show',
    ]);

    return [
      Column::make('DT_RowIndex')
        ->title(trans('#'))
        ->orderable(false)
        ->searchable(false)
        ->width('5%')
        ->addClass('text-center'),
      Column::make('nim')
        ->title(trans('NIM'))
        ->addClass('text-center'),
      Column::make('name')
        ->title(trans('Nama'))
        ->addClass('text-center'),
      Column::make('major_id')
        ->title(trans('Program Studi'))
        ->addClass('text-center'),
      Column::make('status')
        ->title(trans('Status'))
        ->addClass('text-center'),
      Column::make('student_status')
        ->title(trans('Keaktifan'))
        ->orderable(false)
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
    return 'Student_' . date('YmdHis');
  }
}
