@can('grades.show')
<a href="{{ route('grades.show', $row) }}" class="text-success me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.grades.show') }}"><i class="fa fa-sm fa-eye"></i></a>
@endcan

@vite('resources/js/utils/tooltip.js')
