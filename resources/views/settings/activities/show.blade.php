@extends('layouts.app')
@section('title', trans('page.activities.title'))
@section('hero')
<div class="content content-full">
  <div class="content-heading">
    <div class="d-flex justify-content-between align-items-sm-center">
      {{ trans('page.activities.title') }}
      <a href="{{ route('activities.index') }}" class="btn btn-sm btn-block-option text-danger">
        <i class="fa fa-xs fa-chevron-left me-1"></i>
        {{ trans('button.back') }}
      </a>
    </div>
    <nav class="breadcrumb push my-0">
      {{ Breadcrumbs::render('activities.show', $activity) }}
    </nav>
  </div>
</div>
@endsection
@section('content')
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">
      {{ trans('page.activities.show') }}
    </h3>
  </div>
  <div class="block-content">

    <div class="row items-push">
      <div class="col-lg-7">

        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Judul') }}
            <span class="fw-semibold">{{ $activity->log_name }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-start">
            <div>
              {{ trans('Deskripsi') }}
            </div>
            <div class="fw-semibold text-end ms-3">
              {{ $activity->description }}
            </div>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Pelaku') }}
            <span class="fw-semibold">{{ $activity->causer->name }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Waktu') }}
            <span class="fw-semibold">{{ $activity->created_at->format('D, d M Y H:i:s') }}</span>
          </li>
        </ul>

        {{-- @foreach ($activity->properties as $item)
          <div class="my-3">
            <div class="table-responsive">
              <table class="table table-striped table-vcenter">
                <thead>
                  <tr>
                    <th>Data</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>{{ $item->data['name'] }}</td>
        </tr>
        </tbody>
        </table>
      </div>
    </div>
    @endforeach --}}

  </div>
</div>

</div>
</div>
@endsection
