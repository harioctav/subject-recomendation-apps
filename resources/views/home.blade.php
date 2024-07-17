@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">{{ __('Dashboard') }}</div>

        <div class="card-body">
          @if (session('status'))
          @include('components.success-alert')
          @endif

          {{ __('You are logged in!') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
