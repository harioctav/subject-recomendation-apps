<?php

namespace App\DataTables\Scopes;

use App\Helpers\Helper;
use Yajra\DataTables\Contracts\DataTableScope;
use Yajra\DataTables\Utilities\Request;

class GlobalFilter implements DataTableScope
{
  public function __construct(
    protected Request $request
  ) {
    # code...
  }

  /**
   * Apply a query scope.
   *
   * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
   * @return mixed
   */
  public function apply($query)
  {
    $filters = [
      'status',
      'degree',
      'grade',
      'student_status',
      'note',
      'semester'
    ];

    foreach ($filters as $field) {
      if ($this->request->has($field) && $this->request->get($field) !== null) {
        if ($this->request->get($field) !== Helper::ALL) {
          $query->where($field, $this->request->get($field));
        }
      }
    }

    return $query;
  }
}
