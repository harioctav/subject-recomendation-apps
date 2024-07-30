<div class="modal fade" id="modal-select-student" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('grades.export') }}" method="POST" onsubmit="return disableSubmitButton()">
        @csrf

        <div class="block block-rounded shadow-none mb-0">
          <div class="block-header block-header-default">
            <h3 class="block-title">{{ trans('Pilih Mahasiswa') }}</h3>
            <div class="block-options">
              <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
          <div class="block-content fs-sm">

            <div class="mb-4">
              <label for="student_id" class="form-label">{{ trans('Mahasiswa') }}</label>
              <span class="text-danger">*</span>
              <select name="student_id" id="student_id" class="js-select2 form-select @error('student_id') is-invalid @enderror" data-placeholder="{{ trans('Pilih Mahasiswa') }}" style="width: 100%;" data-old="{{ old('student_id') }}" data-container="#modal-select-student">
                <option></option>
                @foreach ($students as $item)
                <option value="{{ $item->id }}" data-uuid="{{ $item->uuid }}" @if (old('student_id')==$item->id) selected @endif>{{ $item->name }}</option>
                @endforeach
              </select>
              @error('student_id')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          </div>
          <div class="block-content block-content-full block-content-sm text-end border-top">
            <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">
              Close
            </button>
            <button type="submit" class="btn btn-sm btn-alt-primary" data-bs-dismiss="modal" id="submit-button">
              {{ trans('Cetak Transkrip Nilai') }}
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
