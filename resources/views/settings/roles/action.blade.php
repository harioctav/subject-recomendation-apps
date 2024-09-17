@if($name === RoleType::ADMINISTRATOR->value)
<span class="badge text-danger">{{ trans('Tidak Bisa Diubah') }}</span>
@else
@can('roles.edit')
<a href="{{ route('roles.edit', $uuid) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.roles.edit') }}"><i class="fa fa-sm fa-pencil"></i></a>
@endcan
@can('roles.destroy')
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-danger delete-roles" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.roles.delete') }}"><i class="fa fa-sm fa-trash"></i></a>
@endcan
@endif

@vite('resources/js/utils/tooltip.js')
