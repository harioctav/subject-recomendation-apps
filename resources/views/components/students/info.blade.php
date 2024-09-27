<div class="row items-push">
  <div class="col-md-6 col-xl-3">
    <div class="block block-rounded text-center my-0">
      <div class="block-content block-content-full bg-gd-dusk">
        <img class="img-avatar img-avatar-thumb" src="{{ $detail['student']->getAvatar() }}" alt="{{ trans('Foto Avatar') }}">
      </div>
      <div class="block-content block-content-full">
        <div class="fw-semibold mb-1">{{ $detail['student']->name }}</div>
        <div class="fs-sm text-muted mb-3">{{ $detail['student']->nim }}</div>
        <div class="fs-sm text-muted">{{ __('Program Studi') }}</div>
        <div class="fw-semibold">{{ $detail['student']->major->name }}</div>
      </div>
      <div class="block-content block-content-full block-content-sm bg-body-light mb-3">
        <span class="fw-semibold fs-sm text-elegance">{{ $detail['student']->email ?: '--' }}</span>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-9">
    <div class="block block-rounded">
      <div class="block-header block-header-default">
        <h3 class="block-title">{{ trans('Perkembangan Akademik Mahasiswa') }}</h3>
      </div>
      <div class="block-content">

        <div class="row items-push text-center">
          <div class="col-6 col-md-3">
            <!-- Pie Chart Container -->
            <div class="js-pie-chart pie-chart my-3" data-percent="{{ $detail['percentace'] }}" data-line-width="4" data-size="100" data-bar-color="#65a30d" data-track-color="#e9e9e9">
              <span>IPK<br><small class="text-muted">{{ $detail['gpa'] }}</small></span>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="block-content block-content-full">
              <div class="fs-2 fw-bold">{{ $detail['total_recommended_credits'] }}
              </div>
              <div class="fs-sm fw-semibold text-muted">{{ trans('Jumlah SKS Diambil') }}</div>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="block block-rounded block-link-shadow">
              <div class="block-content block-content-full">
                <div class="fs-2 fw-bold text-success">{{ $detail['total_completed_course_credit'] }}</div>
                <div class="fs-sm fw-semibold text-muted">{{ trans('Jumlah SKS Lulus') }}</div>
              </div>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="block block-rounded block-link-shadow">
              <div class="block-content block-content-full">
                <div class="fs-2 fw-bold">{{ $detail['total_course_remainder'] }}</div>
                <div class="fs-sm fw-semibold text-muted">SKS Belum Ditempuh <span class="text-success">Kelulusan</span></div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
