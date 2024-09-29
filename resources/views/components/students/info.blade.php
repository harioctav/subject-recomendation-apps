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

      <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
        <div class="block block-rounded block-link-shadow">
          <div class="block-content block-content-full">
            <div class="fs-2 fw-bold">{{ $detail['estimated_remaining_semesters'] }}</div>
            <div class="fs-sm fw-semibold text-muted">
              {{ trans('Estimasi Sisa Semester') }}
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
  <div class="col-md-6 col-xl-9">
    <div class="py-1 my-0 mb-3">
      <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
        {{ trans('Informasi Kemajuan Akademik') }}
      </p>
    </div>

    <div class="row items-center text-center">
      <div class="col-6 col-md-3">
        <!-- Pie Chart Container -->
        <div class="js-pie-chart pie-chart my-3" data-percent="{{ $detail['percentage'] }}" data-line-width="4" data-size="100" data-bar-color="#65a30d" data-track-color="#e9e9e9">
          <span>IPK<br><small class="text-muted">{{ $detail['gpa'] }}</small></span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
          <div class="block-content block-content-full">
            <div class="fs-2 fw-bold">{{ $detail['credit_has_been_taken'] }}</div>
            <div class="fs-sm fw-semibold text-muted">
              {{ trans('Total SKS Sudah Ditempuh') }}
            </div>
          </div>
        </a>
      </div>
      <div class="col-6 col-md-3">
        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
          <div class="block block-rounded block-link-shadow">
            <div class="block-content block-content-full">
              <div class="fs-2 fw-bold text-warning">{{ $detail['total_credit_not_yet_taken'] }}</div>
              <div class="fs-sm fw-semibold text-muted">
                {{ trans('Sisa SKS Belum Ditempuh') }}
              </div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6 col-md-3">
        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
          <div class="block block-rounded block-link-shadow">
            <div class="block-content block-content-full">
              <div class="fs-2 fw-bold">{{ $detail['total_course_credit'] }}</div>
              <div class="fs-sm fw-semibold text-muted">
                {{ trans('Total SKS Wajib Tempuh') }}
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="row text-center mb-0">
      <div class="col-6 col-md-8">
        <ul class="list-group push">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Total SKS Yang Sedang Diambil') }}
            <span class="fw-semibold text-elegance">{{ $detail['credit_being_taken'] ?: '-' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Total SKS Yang Sudah Lulus') }}
            <span class="fw-semibold text-success">{{ $detail['credit_has_been_passed'] ?: '-' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ trans('Total SKS Yang Tidak Lulus') }}
            <span class="fw-semibold text-danger">{{ $detail['credit_need_improvement'] ?: '-' }}</span>
          </li>
        </ul>
      </div>
      <div class="col-6 col-md-4">
        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
          <div class="block block-rounded block-link-shadow">
            <div class="block-content block-content-full">
              <div class="fs-2 fw-bold">{{ $detail['total_credit_not_yet_taken_by_passed'] }}</div>
              <div class="fs-sm fw-semibold text-muted">
                {{ trans('Total SKS Belum Ditempuh Sisa Kelulusan') }}
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="py-1 my-0 mb-3">
      <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
        {{ trans('Matakuliah Pilihan Untuk Prodi ') . $detail['student']->major->name }}
      </p>
    </div>

    <ul class="list-group push">
      <li class="list-group-item d-flex justify-content-between align-items-center">
        {{ trans('Jumlah Matakuliah') }}
        <span class="fw-semibold">{{ $student->major->getFormattedElectiveSemesters()['total'] }}</span>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        {{ trans('Berada di Semester') }}
        <span class="fw-semibold">{{ $student->major->getFormattedElectiveSemesters()['semester'] }}</span>
      </li>
      @can('majors.show')
      <li class="list-group-item d-flex justify-content-between align-items-center">
        {{ trans('Info Lebih Lanjut') }}
        <span class="fw-semibold">
          Detail
          <a href="{{ route('majors.show', $student->major) }}">{{ $student->major->name }}</a>
        </span>
      </li>
      @endcan
    </ul>

  </div>
</div>
