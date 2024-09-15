@can('activities.show')
<a href="{{ route('activities.show', $model) }}" class="text-success me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.activities.show') }}"><i class="fa fa-sm fa-eye"></i></a>
@endcan

@vite('resources/js/utils/tooltip.js')
