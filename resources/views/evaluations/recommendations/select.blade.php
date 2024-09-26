@if($model->grade !== GradeType::A->value)
@if($model->grade === GradeType::E->value && $model->recommendation_note === RecommendationStatus::DALAM_PERBAIKAN->value)
x
@elseif($model->recommendation_note === RecommendationStatus::REQUEST_PERBAIKAN->value)
x
@else
<input type="checkbox" class="select-subject-checkbox" name="courses[]" data-sks="{{ $model->course_credit }}" value="{{ $model->id }}">
@endif
@else
x
@endif
