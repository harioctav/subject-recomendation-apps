@can('recommendations.create')
<a href="{{ route('recommendations.create', $model) }}" class="text-primary me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.recommendations.create') }}">
  <i class="fa fa-plus fa-sm"></i>
</a>
@endcan

@can('recommendations.show')
<a href="{{ route('recommendations.show', $model) }}" class="text-success me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.recommendations.show') }}">
  <i class="fa fa-eye fa-sm"></i>
</a>
@endcan

@vite('resources/js/utils/tooltip.js')
