<div class="dropdown">
  <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" id="dropdown-actions-{{ $uuid }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-fw fa-ellipsis-v"></i>
  </button>
  <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-actions-{{ $uuid }}">
    @if (!$deleted_at)
    @can('students.edit')
    <a class="dropdown-item" href="{{ route('students.edit', $uuid) }}">
      <i class="fa fa-pencil fa-fw me-2"></i>
      {{ trans('page.students.edit', ['students' => '']) }}
    </a>
    @endcan
    @can('students.show')
    <a class="dropdown-item" href="{{ route('students.show', $uuid) }}">
      <i class="fa fa-eye fa-fw me-2"></i>
      {{ trans('page.students.show', ['students' => '']) }}
    </a>
    @endcan
    @if(!$model->recommendations()->exists())
    @can('students.destroy')
    <a class="dropdown-item delete-students" href="javascript:void(0)" data-uuid="{{ $uuid }}">
      <i class="fa fa-trash fa-fw me-2"></i>
      {{ trans('page.students.delete', ['students' => '']) }}
    </a>
    @endcan
    @endif
    @else
    @can('students.restore')
    <a class="dropdown-item restore-students" href="javascript:void(0)" data-uuid="{{ $uuid }}">
      <i class="fa fa-undo fa-fw me-2"></i>
      {{ trans('page.students.restore', ['students' => '']) }}
    </a>
    @endcan
    @can('students.delete')
    <a class="dropdown-item delete-permanent-students" href="javascript:void(0)" data-uuid="{{ $uuid }}">
      <i class="fa fa-trash fa-fw me-2"></i>
      {{ trans('page.students.force', ['students' => '']) }}
    </a>
    @endcan
    @endif
  </div>
</div>
