@can('recommendations.destroy')
<a href="javascript:void(0)" data-uuid="{{ $uuid }}" class="text-danger me-1 delete-recommendations" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('page.recommendations.delete') }}"><i class="fa fa-sm fa-trash"></i></a>
@endcan

@vite('resources/js/utils/tooltip.js')
