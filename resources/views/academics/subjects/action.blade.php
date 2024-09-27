<div class="dropdown">
  <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" id="dropdown-actions-{{ $uuid }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-fw fa-ellipsis-v"></i>
  </button>
  <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-actions-{{ $uuid }}">
    @can('subjects.edit')
    <a class="dropdown-item" href="{{ route('subjects.edit', $uuid) }}">
      <i class="fa fa-pencil fa-fw me-2"></i>
      {{ trans('page.subjects.edit', ['subjects' => '']) }}
    </a>
    @endcan
    @can('subjects.show')
    <a class="dropdown-item show-subjects" href="javascript:void(0)" data-uuid="{{ $uuid }}">
      <i class="fa fa-eye fa-fw me-2"></i>
      {{ trans('page.subjects.show', ['subjects' => '']) }}
    </a>
    @endcan
    @can('subjects.destroy')
    <a class="dropdown-item delete-subjects" href="javascript:void(0)" data-uuid="{{ $uuid }}">
      <i class="fa fa-trash fa-fw me-2"></i>
      {{ trans('page.subjects.delete', ['subjects' => '']) }}
    </a>
    @endcan
  </div>
</div>
