@can('grades.edit')
<a href="{{ route('grades.edit', ['grade' => $uuid, 'student' => $model->student]) }}" class="text-warning me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.grades.edit') }}">
  <i class="fa fa-sm fa-pencil"></i>
</a>
@endcan

@can('grades.destroy')
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-danger me-1 delete-grades" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.grades.delete') }}">
  <i class="fa fa-sm fa-trash"></i>
</a>
@endcan
