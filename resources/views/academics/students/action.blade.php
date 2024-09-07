@if (!$deleted_at)
@can('students.edit')
<a href="{{ route('students.edit', $uuid) }}" class="text-warning me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.students.edit') }}"><i class="fa fa-sm fa-pencil"></i></a>
@endcan
@can('students.show')
<a href="{{ route('students.show', $uuid) }}" class="text-modern me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.students.show') }}"><i class="fa fa-sm fa-eye"></i></a>
@endcan
@if(!$model->recommendations()->exists())
@can('students.destroy')
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-danger me-1 delete-students" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.students.delete') }}"><i class="fa fa-sm fa-trash"></i></a>
@endcan
@endif
@else
@can('students.restore')
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-success me-1 restore-students" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.students.restore') }}"><i class="fa fa-sm fa-undo"></i></a>
@endcan
@can('students.delete')
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-danger me-1 delete-permanent-students" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.students.force') }}"><i class="fa fa-sm fa-trash"></i></a>
@endcan
@endif

@vite('resources/js/utils/tooltip.js')
