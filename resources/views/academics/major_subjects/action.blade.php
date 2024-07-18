@can('majors.subjects.destroy')
<a href="javascript:void(0)" data-delete-url="{{ route('majors.subjects.destroy', ['major' => $model->major->uuid, 'subject' => $model->subject->uuid]) }}" data-major-id="{{ $model->major->uuid }}" data-subject-id="{{ $model->subject->uuid }}" class="text-danger me-1 delete-major-subjects" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.majors.subjects.destroy') }}">
  <i class="fa fa-sm fa-trash"></i>
</a>
@endcan

@vite('resources/js/utils/tooltip.js')
