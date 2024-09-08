<?php

namespace App\DataTables\Scopes;

use App\Helpers\Helper;
use Yajra\DataTables\Utilities\Request;
use Yajra\DataTables\Contracts\DataTableScope;

class GradeFilter implements DataTableScope
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
    if ($this->request->has('grade') && $this->request->get('grade') !== null) {
      $grade = $this->request->get('grade');
      if ($grade !== Helper::ALL) {
        $query->where('grade', $grade);
      }
    }

    return $query;
  }
}
