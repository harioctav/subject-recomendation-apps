@if ($errors->any())
<div class="my-3">
  <div class="alert alert-warning alert-dismissible d-flex align-items-center" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <i class="fa fa-sm fa-exclamation-triangle me-2"></i>
    @foreach ($errors->all() as $error)
    <p class="mb-0">
      {{ $error }}
    </p>
    @endforeach
  </div>
</div>
@endif
