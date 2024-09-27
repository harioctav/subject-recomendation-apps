@if ($name === RoleType::ADMINISTRATOR->value)
<span class="badge text-danger">{{ trans('Tidak Bisa Diubah') }}</span>
@else
@canany(['roles.edit', 'roles.destroy'])
<div class="dropdown">
  <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" id="dropdown-default-outline-primary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-fw fa-ellipsis-v"></i>
  </button>
  <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-default-outline-primary">
    @can('roles.edit')
    <a href="{{ route('roles.edit', $uuid) }}" class="dropdown-item">
      <i class="fa fa-pencil fa-fw me-2"></i>
      {{ trans('page.roles.edit', ['roles' => '']) }}
    </a>
    @endcan
    @can('roles.destroy')
    <a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="dropdown-item delete-roles">
      <i class="fa fa-trash fa-fw me-2"></i>
      {{ trans('page.roles.delete', ['roles' => '']) }}
    </a>
    @endcan
  </div>
</div>
@endcanany
@endif
