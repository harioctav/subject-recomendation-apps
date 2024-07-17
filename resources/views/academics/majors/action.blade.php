@can('majors.edit')
<a href="{{ route('majors.edit', $uuid) }}" class="text-warning me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.majors.edit') }}"><i class="fa fa-sm fa-pencil"></i></a>
@endcan
@can('majors.show')
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-modern me-2 show-majors" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.majors.show') }}"><i class="fa fa-sm fa-eye"></i></a>
@endcan
@can('majors.destroy')
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-danger me-1 delete-majors" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.majors.delete') }}"><i class="fa fa-sm fa-trash"></i></a>
@endcan

@vite('resources/js/utils/tooltip.js')
