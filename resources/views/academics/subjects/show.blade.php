<div class="modal fade" id="modal-show-subject" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popin modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="block block-rounded shadow-none mb-0">
        <div class="block-header block-header-default">
          <h3 class="block-title">{{ trans('page.subjects.show', ['subjects' => trans('page.subjects.title')]) }}</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa fa-times"></i>
            </button>
          </div>
        </div>
        <div class="block-content fs-sm">

          <ul class="list-group push">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Kode') }}
              <span class="fw-semibold" id="subject-code"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Matakuliah') }}
              <span class="fw-semibold" id="subject-name"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Jumlah SKS') }}
              <span class="fw-semibold" id="subject-course-credit"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Waktu Ujian') }}
              <span class="fw-semibold" id="subject-exam-time"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ trans('Status') }}
              <span class="fw-semibold" id="subject-status"></span>
            </li>
            <li class="list-group-item">
              <div class="d-flex justify-content-between align-items-center">
                <span>{{ trans('Keterangan') }}</span>
                <span class="fw-semibold text-end ms-2" id="subject-note" style="word-break: break-word; max-width: 60%;"></span>
              </div>
            </li>
          </ul>

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
