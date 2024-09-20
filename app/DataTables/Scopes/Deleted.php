<?php

namespace App\DataTables\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;
use Yajra\DataTables\Utilities\Request;

class Deleted implements DataTableScope
{
  public function __construct(
    protected Request $request
  ) {
    // 
  }

  /**
   * Apply a query scope.
   *
   * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
   * @return mixed
   */
  public function apply($query)
  {
    $filters = ['deleted'];

    foreach ($filters as $field) {
      if ($this->request->has($field) && $this->request->get($field) === 'true') {
        $query->onlyTrashed();
      }
    }

    return $query;
  }
}
