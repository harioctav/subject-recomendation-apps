<?php

namespace App\DataTables\Evaluations;

use App\DataTables\Scopes\GlobalFilter;
use App\Helpers\Helper;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class GradeDataTable extends DataTable
{
  public $studentId;

  public function __construct($studentId = null)
  {
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
      'subject' => function ($query) {
        $query->with('majors');
      },
      // Pastikan kita mengambil semua kolom yang diperlukan dari tabel recommendations
      'recommendations'
    ]);

    return (new EloquentDataTable($query))
      ->addIndexColumn()
      ->editColumn('student_id', fn($row) => $row->student->name)
      ->editColumn('subject_id', fn($row) => $row->subject->name)
      ->addColumn('subject_code', fn($row) => $row->subject->code)
      ->editColumn('mutu', fn($row) => $row->mutuLabel)
      ->addColumn('major', fn($row) => $row->student->major->name)
      ->addColumn('semester', fn($row) => $row->semester)
      ->filterColumn('subject_id', function ($query, $keyword) {
        $query->whereHas('subject', function ($query) use ($keyword) {
          $query->where('name', 'LIKE', "%{$keyword}%");
        });
      })
      ->addColumn('action', function ($row) {
        return view('evaluations.grades.option', [
          'uuid' => $row->uuid,
          'model' => $row,
        ]);
      })
      ->rawColumns(['action']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Grade $model): QueryBuilder
  {
    $query = $model->newQuery()
      ->latest()
      ->where('student_id', $this->studentId);

    if ($this->request()->has('grade')) {
      $gradeFilter = new GlobalFilter($this->request());
      $gradeFilter->apply($query);
    }

    return $query;
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('grade-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      //->dom('Bfrtip')
      ->ajax([
        'data' => 'function(d) { d.grade = $("#grade").val(); }'
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
      'grades.edit',
      'grades.destroy',
    ]);

    return [
      Column::make('DT_RowIndex')
        ->title(trans('#'))
        ->orderable(false)
        ->searchable(false)
        ->width('5%')
        ->addClass('text-center'),
      Column::make('subject_code')
        ->title(trans('Kode Matkul'))
        ->addClass('text-center'),
      Column::make('subject_id')
        ->title(trans('Matakuliah'))
        ->addClass('text-center'),
      Column::make('semester')
        ->title(trans('Semester'))
        ->addClass('text-center'),
      Column::make('grade')
        ->title(trans('Nilai'))
        ->addClass('text-center'),
      Column::make('mutu')
        ->title(trans('Mutu'))
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
    return 'Grade_' . date('YmdHis');
  }
}
