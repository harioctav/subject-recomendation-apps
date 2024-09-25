<div class="modal fade" id="modal-fadein" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="block block-rounded shadow-none mb-0">
        <div class="block-header block-header-default">
          <h3 class="block-title">
            {{ trans('Import Data Nilai Mahasiswa') }}
          </h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa fa-times"></i>
            </button>
          </div>
        </div>
        <form action="{{ route('grades.import', ['student' => $student]) }}" method="POST" enctype="multipart/form-data" onsubmit="return disableSubmitButton()">
          @csrf
          <div class="block-content fs-sm">

            <div class="mb-4">
              <label class="form-label" for="file">{{ trans('Pilih File Import') }}</label>
              <input class="form-control @error('file') is-invalid @enderror" type="file" accept=".xls,.xlsx" id="file" name="file">
              @error('file')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <span class="text-muted">
                {{ trans('page.import') }}
                <strong>
                  <a href="{{ asset('assets/excels/template-subjects-to-major.xlsx') }}">Download File.</a>
                </strong>
              </span>
            </div>

          </div>
          <div class="block-content block-content-full block-content-sm text-end border-top">
            <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">
              {{ trans('Tutup') }}
            </button>
            <button type="submit" class="btn btn-alt-primary" data-bs-dismiss="modal" id="submit-button">
              {{ trans('Import Nilai Mahasiswa') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
