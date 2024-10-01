<!-- Edit Major Subject Modal -->
<div class="modal fade" id="edit-major-subjects-form" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="update-major-subjects">
      @csrf
      @method('PUT')

      <div class="modal-content">
        <div class="block block-rounded shadow-none mb-0">
          <div class="block-header block-header-default">
            <h3 class="block-title">
              {{ trans('Ubah Semester') }}
            </h3>
            <div class="block-options">
              <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
          <div class="block-content fs-sm">

            <div class="mb-4">
              <label for="semester" class="form-label">Semester</label>
              <input type="number" class="form-control" id="semester" name="semester" min="1" max="8" required>
              <div id="semester-error" class="invalid-feedback"></div>
            </div>

          </div>
          <div class="block-content block-content-full block-content-sm text-end border-top">
            <button type="button" class="btn btn-sm btn-alt-danger" data-bs-dismiss="modal">
              {{ trans('button.cancel') }}
            </button>
            <button type="submit" class="btn btn-sm btn-alt-warning" data-bs-dismiss="modal" id="submit-button">
              {{ trans('Simpan Perubahan') }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
