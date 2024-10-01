<div class="dropdown">
  <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" id="dropdown-actions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-fw fa-ellipsis-v"></i>
  </button>
  <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-actions">
    @can('majors.subjects.update')
    <a class="dropdown-item edit-major-subject" href="javascript:void(0)" data-update-url="{{ route('majors.subjects.update', ['major' => $model->major->uuid, 'subject' => $model->subject->uuid ]) }}" data-semester="{{ $model->semester }}">
      <i class="fa fa-sm fa-edit me-1"></i> {{ trans('page.majors.subjects.edit') }}
    </a>
    @endcan
    @can('majors.subjects.destroy')
    <a class="dropdown-item text-danger delete-major-subjects" href="javascript:void(0)" data-delete-url="{{ route('majors.subjects.destroy', ['major' => $model->major->uuid, 'subject' => $model->subject->uuid]) }}" data-major-id="{{ $model->major->uuid }}" data-subject-id="{{ $model->subject->uuid }}">
      <i class="fa fa-sm fa-trash me-1"></i> {{ trans('page.majors.subjects.destroy') }}
    </a>
    @endcan
  </div>
</div>
