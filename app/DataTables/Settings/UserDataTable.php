<?php

namespace App\DataTables\Settings;

use App\Models\User;
use App\Helpers\Helper;
use App\Helpers\Enums\RoleType;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Services\User\UserService;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class UserDataTable extends DataTable
{
  /**
   * Create a new datatables instance.
   *
   * @return void
   */
  public function __construct(
    protected UserService $userService,
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
      ->editColumn('roles', fn ($row) => $row->getRoleName())
      ->editColumn('status', fn ($row) => $row->statusLabel)
      ->editColumn('phone', fn ($row) => $row->phone ?: '--')
      ->addColumn('edit_status', 'settings.users.status')
      ->addColumn('action', 'settings.users.action')
      ->rawColumns([
        'roles',
        'status',
        'action',
        'edit_status',
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(User $model): QueryBuilder
  {
    return $this->userService->getQuery()->oldest('name')->whereNotAdmin();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('user-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->ajax([
        'url' => route('users.index'),
        'type' => 'GET',
        'data' => "
          function(data) {
            data.roles = $('select[name=roles]').val();
            data.status = $('select[name=status]').val();
          }"
      ])
      ->addTableClass([
        'table',
        'table-striped',
        'table-bordered',
        'table-hover',
        'table-vcenter',
      ])
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
    $visibility = isRoleName() === RoleType::ADMINISTRATOR->value ? true : false;

    $action = Helper::checkPermissions([
      'users.edit',
      'users.destroy',
    ]);

    return [
      Column::make('DT_RowIndex')
        ->title(trans('#'))
        ->orderable(false)
        ->searchable(false)
        ->width('5%')
        ->addClass('text-center'),
      Column::make('name')
        ->title(trans('Nama'))
        ->addClass('text-center'),
      Column::make('email')
        ->title(trans('Email'))
        ->addClass('text-center'),
      Column::make('roles')
        ->title(trans('Peran'))
        ->addClass('text-center'),
      Column::make('phone')
        ->title(trans('No. Hp'))
        ->addClass('text-center'),
      Column::make('status')
        ->title(trans('Status'))
        ->addClass('text-center'),
      Column::make('edit_status')
        ->title(trans('Ubah Status'))
        ->visible($visibility)
        ->addClass('text-center'),
      Column::computed('action')
        ->title(trans('Opsi'))
        ->exportable(false)
        ->printable(false)
        ->visible($action)
        ->width('5%')
        ->addClass('text-center'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'User_' . date('YmdHis');
  }
}
