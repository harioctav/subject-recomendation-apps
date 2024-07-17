@can('users.edit')
@if (me()->id == $id)
<span class="badge text-danger">{{ trans('Ubah Data Diri Di Halaman Profil') }}</span>
@else
<a href="{{ route('users.edit', $uuid) }}" class="text-warning me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.users.edit') }}"><i class="fa fa-sm fa-pencil"></i></a>
@endif
@endcan
@can('users.destroy')
@if ($model->hasRole(RoleType::ADMINISTRATOR->value))
<span class="badge text-danger">{{ trans('Tidak Bisa Dihapus') }}</span>
@else
@if (!$status)
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-danger me-2 delete-users" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.users.delete') }}"><i class="fa fa-sm fa-trash"></i></a>
@endif
@endif
@endcan

@vite('resources/js/utils/tooltip.js')
