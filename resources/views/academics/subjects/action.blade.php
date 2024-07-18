@can('subjects.edit')
<a href="{{ route('subjects.edit', $uuid) }}" class="text-warning me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.subjects.edit') }}"><i class="fa fa-sm fa-pencil"></i></a>
@endcan
@can('subjects.show')
<a href="{{ route('subjects.show', $uuid) }}" class="text-modern me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.subjects.show') }}"><i class="fa fa-sm fa-eye"></i></a>
@endcan
@can('subjects.destroy')
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-danger me-1 delete-subjects" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.subjects.delete') }}"><i class="fa fa-sm fa-trash"></i></a>
@endcan

@vite('resources/js/utils/tooltip.js')