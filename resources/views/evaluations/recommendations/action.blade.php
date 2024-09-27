<div class="dropdown">
  <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" id="dropdown-default-outline-primary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-fw fa-ellipsis-v"></i>
  </button>
  <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-default-outline-primary">
    @can('recommendations.show')
    <a href="{{ route('recommendations.show', $row) }}" class="dropdown-item">
      <i class="fa fa-eye fa-sm me-2"></i>
      {{ trans('Detail Mahasiswa') }}
    </a>
    @endcan
  </div>
</div>
