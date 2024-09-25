<div class="modal fade" id="modal-import-subjects" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('majors.subjects.import') }}" enctype="multipart/form-data" method="POST" onsubmit="return disableSubmitButton()">
      @csrf
      <div class="modal-content">
        <div class="block block-rounded shadow-none mb-0">
          <div class="block-header block-header-default">
            <h3 class="block-title">{{ trans('Import Data Matakuliah Program Studi') }}</h3>
            <div class="block-options">
              <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
          <div class="block-content fs-sm">

            <div class="mb-4">
              <label class="form-label" for="file">{{ trans('Pilih File Excel') }}</label>
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
            <button type="button" class="btn btn-alt-danger" data-bs-dismiss="modal">
              {{ trans('Batalkan') }}
            </button>
            <button type="submit" class="btn btn-alt-primary" data-bs-dismiss="modal" id="submit-button">
              {{ trans('Import Data') }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
