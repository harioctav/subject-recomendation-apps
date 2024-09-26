<?php

namespace App\DataTables\Evaluations;

use App\Helpers\Enums\GradeType;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SubjectDataTable extends DataTable
{
  protected $studentId;
  protected $majorId;

  public function __construct($studentId)
  {
    $this->studentId = $studentId;
    $this->majorId = Student::findOrFail($studentId)->major_id;
  }


  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('grade', function ($row) {
        if ($row->grade === GradeType::E->value) {
          return "<span style='color: red;'>{$row->grade}</span>";
        }
        return $row->grade ?: '-';
      })
      ->editColumn('note', fn($row) => $row->note ?: '-')
      ->addColumn('select', 'evaluations.recommendations.select')
      ->rawColumns([
        'select',
        'grade'
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Subject $model): QueryBuilder
  {
    $query = $model->newQuery()
      ->join('major_subject', 'subjects.id', '=', 'major_subject.subject_id')
      ->leftJoin('grades', function ($join) {
        $join->on('subjects.id', '=', 'grades.subject_id')
          ->where('grades.student_id', '=', $this->studentId);
      })
      ->leftJoin('recommendations', function ($join) {
        $join->on('subjects.id', '=', 'recommendations.subject_id')
          ->where('recommendations.student_id', '=', $this->studentId);
      })
      ->where('major_subject.major_id', $this->majorId);
    // ->where('subjects.code', 'like', '%EKMA4313%');

    if ($this->request()->get('grade_filter') == null) {
      $query->where(function ($query) {
        $query->whereNull('recommendations.id')
          ->orWhere('grades.grade', 'E');
      });
    }

    // Add grade filter
    if ($this->request()->has('grade_filter')) {
      $gradeFilter = $this->request()->get('grade_filter');
      $query->where('grades.grade', $gradeFilter);
    }

    if ($this->request()->has('search_custom')) {
      $search = $this->request()->get('search_custom');
      $query->where('subjects.code', 'like', "%{$search}%");
    }

    $query->select(
      'subjects.id',
      'subjects.name',
      'subjects.code',
      'subjects.note',
      DB::raw('CAST(subjects.course_credit AS SIGNED) as course_credit'),
      'subjects.status',
      'major_subject.semester as major_semester',
      'grades.grade',
      'recommendations.id as recommendation_id',
      'recommendations.note as recommendation_note'
    )->orderBy('major_semester', 'asc');

    return $query;
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('subject-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->ajax([
        'data' => 'function(data) { 
          data.grade_filter = $("#grade_filter").val();
          data.search_custom = $("#search_custom").val();
        }'
      ])
      ->dom('lrtip')
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
    return [
      Column::computed('select')
        ->title('<input type="checkbox" id="select-all" />')
        ->exportable(false)
        ->printable(false)
        ->orderable(false)
        ->searchable(false)
        ->width('5%')
        ->addClass('text-center'),
      Column::make('major_semester')
        ->title('Semester')
        ->addClass('text-center'),
      Column::make('code')
        ->addClass('text-center')
        ->title('Kode'),
      Column::make('name')
        ->addClass('text-center')
        ->title('Matakuliah'),
      Column::make('grade')
        ->addClass('text-center')
        ->title('Nilai')
        ->escapeHtml(false),
      Column::make('course_credit')
        ->addClass('text-center')
        ->title('SKS'),
      Column::make('recommendation_note')
        ->addClass('text-center')
        ->title('Keterangan'),
      Column::make('status')
        ->addClass('text-center')
        ->title('Status'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Subject_' . date('YmdHis');
  }
}
