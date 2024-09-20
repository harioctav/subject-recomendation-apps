<?php

namespace App\DataTables\Academics;

use App\DataTables\Scopes\GlobalFilter;
use App\Models\Major;
use App\Helpers\Helper;
use Yajra\DataTables\Html\Column;
use App\Services\Major\MajorService;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class MajorDataTable extends DataTable
{
  /**
   * Create a new datatables instance.
   *
   * @return void
   */
  public function __construct(
    protected MajorService $majorService,
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
      ->editColumn('total_course_credit', fn($row) => $row->total_course_credit ?: '--')
      ->addColumn('action', 'academics.majors.action')
      ->rawColumns([
        'action',
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Major $model): QueryBuilder
  {
    $query = $this->majorService->getQuery()->orderBy('name', 'asc')->latest();

    if ($this->request()->has('degree')) {
      $filter = new GlobalFilter($this->request());
      $filter->apply($query);
    }

    return $query;
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('major-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      //->dom('Bfrtip')
      ->ajax([
        'data' => "
          function(data) {
            data.degree = $('select[name=degree]').val();
          }"
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
      'majors.edit',
      'majors.destroy',
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
        ->title(trans('Nama Jurusan'))
        ->addClass('text-center'),
      Column::make('degree')
        ->title(trans('Tingkat atau Jenjang'))
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
    return 'Major_' . date('YmdHis');
  }
}
