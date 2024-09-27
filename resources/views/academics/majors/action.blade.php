<div class="dropdown">
  <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" id="dropdown-actions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-fw fa-ellipsis-v"></i>
  </button>
  <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-actions">
    @can('majors.edit')
    <a class="dropdown-item" href="{{ route('majors.edit', $uuid) }}">
      <i class="fa fa-pencil fa-fw me-2"></i>
      {{ trans('page.majors.edit', ['majors' => '']) }}
    </a>
    @endcan
    @can('majors.show')
    <a class="dropdown-item" href="{{ route('majors.show', $uuid) }}">
      <i class="fa fa-eye fa-fw me-2"></i>
      {{ trans('page.majors.show', ['majors' => '']) }}
    </a>
    @endcan
    @can('majors.destroy')
    <a class="dropdown-item delete-majors" href="javascript:void(0)" data-uuid="{{ $uuid }}">
      <i class="fa fa-trash fa-fw me-2"></i>
      {{ trans('page.majors.delete', ['majors' => '']) }}
    </a>
    @endcan
  </div>
</div>
