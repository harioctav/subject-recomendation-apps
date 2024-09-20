<div class="modal fade" id="modal-fadein" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('students.data') }}" method="POST">
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

          </div>
          <div class="block-content block-content-full block-content-sm text-end border-top">
            <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">
              Close
            </button>
            <button type="submit" class="btn btn-alt-primary" data-bs-dismiss="modal">
              {{ __('Cari Data') }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
