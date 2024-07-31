<?php

namespace App\DataTables\Academics;

use App\Helpers\Helper;
use App\Models\Student;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use App\Services\Student\StudentService;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class StudentDataTable extends DataTable
{
  /**
   * Create a new datatables instance.
   *
   * @return void
   */
  public function __construct(
    protected StudentService $studentService,
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
    $query = $query->with('major');

    return (new EloquentDataTable($query))
      ->addIndexColumn()
      ->editColumn('major_id', fn ($row) => $row->major->name)
      ->editColumn('email', fn ($row) => $row->email ?? "--")
      ->editColumn('status', fn ($row) => $row->statusLabel)
      ->filterColumn('major_id', function ($query, $keyword) {
        $query->whereHas('major', function ($query) use ($keyword) {
          $query->where('name', 'LIKE', "%{$keyword}%");
        });
      })
      ->addColumn('action', 'academics.students.action')
      ->rawColumns([
        'action',
        'status'
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Student $model): QueryBuilder
  {
    return $this->studentService->getQuery();
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
      ->ajax([
        'url' => route('students.index'),
        'type' => 'GET',
        'data' => "
          function(data) {
            data.status = $('select[name=status]').val();
          }"
      ])
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
      'students.edit',
      'students.destroy',
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
    return 'Student_' . date('YmdHis');
  }
}
