@can('recommendations.show')
<a href="{{ route('recommendations.show', $row) }}" class="text-success me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.recommendations.show') }}">
  <i class="fa fa-eye fa-sm"></i>
</a>
@endcan

@vite('resources/js/utils/tooltip.js')
