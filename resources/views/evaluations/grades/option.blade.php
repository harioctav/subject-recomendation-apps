@php
$showEditButton = (
$model->grade === GradeType::A->value ||
($model->grade !== GradeType::A->value && $model->recommendation_note === RecommendationStatus::REQUEST_PERBAIKAN->value) ||
($model->grade === 'E' && $model->recommendation_note === RecommendationStatus::DALAM_PERBAIKAN->value)
)
@endphp

<div class="dropdown">
  <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" id="dropdown-grade-options" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-fw fa-ellipsis-v"></i>
  </button>
  <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-grade-options">
    @if($showEditButton)
    @can('grades.edit')
    <a class="dropdown-item" href="{{ route('grades.edit', ['grade' => $uuid, 'student' => $model->student]) }}">
      <i class="fa fa-pencil fa-fw me-2"></i>
      {{ trans('Ubah') }}
    </a>
    @endcan
    @else
    @can('grades.destroy')
    <a class="dropdown-item delete-grades" href="javascript:void(0)" data-uuid="{{ $uuid }}">
      <i class="fa fa-trash fa-fw me-2"></i>
      {{ trans('Hapus') }}
    </a>
    @endcan
    @endif

    {{-- Add more dropdown items here as needed --}}
  </div>
</div>
