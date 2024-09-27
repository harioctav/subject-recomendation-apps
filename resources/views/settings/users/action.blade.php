<div class="dropdown">
  <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" id="dropdown-actions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-fw fa-ellipsis-v"></i>
  </button>
  <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-actions">
    @can('users.edit')
    @if (me()->id == $id)
    <span class="dropdown-item-text text-danger">{{ trans('Ubah Data Diri Di Halaman Profil') }}</span>
    @else
    <a class="dropdown-item" href="{{ route('users.edit', $uuid) }}">
      <i class="fa fa-pencil fa-fw me-2"></i>
      {{ trans('page.users.edit', [
        'users' => ''
      ]) }}
    </a>
    @endif
    @endcan

    @can('users.destroy')
    @if ($model->hasRole(RoleType::ADMINISTRATOR->value))
    <span class="dropdown-item-text text-danger">{{ trans('Tidak Bisa Dihapus') }}</span>
    @else
    @if (!$status)
    <a class="dropdown-item delete-users" href="javascript:void(0)" data-uuid="{{ $uuid }}">
      <i class="fa fa-trash fa-fw me-2"></i>
      {{ trans('page.users.delete', [
        'users' => ''
      ]) }}
    </a>
    @endif
    @endif
    @endcan
  </div>
</div>
