<div class="modal fade" id="modal-form-input-nim" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="nim-form" action="{{ route('students.data') }}" method="POST">
      @csrf

      <div class="modal-content">
        <div class="block block-rounded shadow-none mb-0">
          <div class="block-header block-header-default">
            <h3 class="block-title">Data Mahasiswa</h3>
            <div class="block-options">
              <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
          <div class="block-content fs-sm">
            <div class="mb-4">
              <label for="nim" class="form-label">{{ trans('NIM') }}</label>
              <span class="text-danger">*</span>
              <input type="text" name="nim" id="nim" value="{{ old('nim') }}" class="form-control @error('nim') is-invalid @enderror" placeholder="{{ trans('Masukkan Nomor Induk Mahasiswa') }}" onkeypress="return onlyNumber(event)">
              @error('nim')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div id="options" style="display: none;">
              <div class="mb-4">
                <button type="button" class="btn btn-alt-primary btn-block" onclick="submitForm('data')">
                  {{ trans('Lihat Data Mahasiswa') }}
                </button>
              </div>
            </div>
          </div>
          <div class="block-content block-content-full block-content-sm text-end border-top">
            <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">
              Close
            </button>
            <button type="button" id="next-btn" class="btn btn-alt-primary" onclick="showOptions()">
              {{ __('Selanjutnya') }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
