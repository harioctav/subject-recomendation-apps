<div class="modal fade" id="modal-show-student" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popin modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-rounded shadow-none mb-0">
        <div class="block-header block-header-default">
          <h3 class="block-title">{{ trans('page.students.show', ['students' => trans('page.students.title')]) }}</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa fa-times"></i>
            </button>
          </div>
        </div>
        <div class="block-content fs-sm">

          <div class="row">
            <div class="col-4">
              <div class="block block-rounded text-center my-0">
                <div class="block-content block-content-full bg-gd-dusk">
                  <img class="img-avatar img-avatar-thumb student-avatar" src="" alt="{{ trans('Foto Mahasiswa') }}">
                </div>
                <div class="block-content block-content-full">
                  <div class="fw-semibold mb-1 student-name"></div>
                  <div class="fs-sm text-muted mb-3 student-nim"></div>
                  <div class="fs-sm text-muted">{{ __('Program Studi') }}</div>
                  <div class="fw-semibold student-major-name"></div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light mb-3">
                  <span class="fw-semibold fs-sm text-elegance student-email"></span>
                </div>
              </div>
            </div>
            <div class="col-8">
              <div class="py-1 my-0 mb-1">
                <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
                  {{ trans('Informasi Umum') }}
                </p>
              </div>

              <ul class="list-group push">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('UPBJJ') }}
                  <span class="fw-semibold student-upbjj"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Registrasi Awal') }}
                  <span class="fw-semibold student-initial-registration-period"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Jurusan Asal') }}
                  <span class="fw-semibold student-origin-department"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Jenis Kelamin') }}
                  <span class="fw-semibold student-gender"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Tempat Lahir') }}
                  <span class="fw-semibold student-birth-place"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Tanggal Lahir') }}
                  <span class="fw-semibold student-birth-day"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Agama') }}
                  <span class="fw-semibold student-religion"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Status Keaktifan') }}
                  <span class="fw-semibold student-status"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Status Pendaftaran') }}
                  <span class="fw-semibold student-status-regis"></span>
                </li>
              </ul>

              <div class="py-1 my-0 mb-1">
                <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
                  {{ trans('Informasi Kontak') }}
                </p>
              </div>
              <ul class="list-group push">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('No. Handphone') }}
                  <span class="fw-semibold student-phone"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Email Pribadi') }}
                  <span class="fw-semibold student-email"></span>
                </li>
              </ul>

              <div class="py-1 my-0 mb-1">
                <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
                  {{ trans('Informasi Orang Tua') }}
                </p>
              </div>
              <ul class="list-group push">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Nama Ibu Kandung') }}
                  <span class="fw-semibold student-parent-name"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('No. Handphone') }}
                  <span class="fw-semibold student-parent-phone"></span>
                </li>
              </ul>

              <div class="py-1 my-0 mb-1">
                <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
                  {{ trans('Informasi Alamat') }}
                </p>
              </div>
              <ul class="list-group push">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Provinsi') }}
                  <span class="fw-semibold student-province"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Kabupaten/Kota') }}
                  <span class="fw-semibold student-regency"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Kecamatan') }}
                  <span class="fw-semibold student-district"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Desa/Kelurahan') }}
                  <span class="fw-semibold student-village"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  {{ trans('Kode Pos') }}
                  <span class="fw-semibold student-postal-code"></span>
                </li>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between align-items-center">
                    <span>{{ trans('Alamat') }}</span>
                    <span class="fw-semibold text-end ms-2 student-address" style="word-break: break-word; max-width: 60%;"></span>
                  </div>
                </li>
              </ul>
            </div>
          </div>

        </div>
        <div class="block-content block-content-full block-content-sm text-end border-top">
          <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
