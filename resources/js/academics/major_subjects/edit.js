$(document).ready(function () {
    const $document = $(document);
    const $modal = $("#edit-major-subjects-form");
    const $form = $("#update-major-subjects");
    const $semester = $("#semester");
    const $semesterError = $("#semester-error");
    const $submitButton = $("#submit-button");

    $document.on("click", ".edit-major-subject", handleEditClick);
    $semester.on("input", () => validateSemester($semester));
    $form.on("submit", handleFormSubmit);

    function handleEditClick() {
        const urlUpdateData = $(this).data("update-url");
        const semester = $(this).data("semester");

        $form.attr("action", urlUpdateData);
        $semester.val(semester);
        $modal.modal("show");
    }

    function validateSemester(input) {
        const value = input.val();

        if (value === "") {
            showError(input, "Semester harus diisi.");
        } else if (isNaN(value) || value < 1) {
            showError(input, "Semester harus berupa angka positif.");
        } else if (value > 8) {
            showError(input, "Semester tidak boleh lebih dari 8.");
        } else {
            hideError(input);
        }
    }

    function showError(input, message) {
        input.addClass("is-invalid");
        $semesterError.text(message).show();
        $submitButton.prop("disabled", true);
    }

    function hideError(input) {
        input.removeClass("is-invalid");
        $semesterError.text("").hide();
        $submitButton.prop("disabled", false);
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        validateSemester($semester);

        if ($form.find(".is-invalid").length > 0) return;

        $.ajax({
            type: "PUT",
            url: $form.attr("action"),
            data: $form.serialize(),
            success: handleSuccess,
            error: handleError,
        });
    }

    function handleSuccess(response) {
        if (response.success) {
            $modal.modal("hide");
            showSuccessAlert("Data berhasil diubah!");
            refreshTable();
        } else {
            showErrorAlert("Terjadi kesalahan: " + response.message);
        }
    }

    function handleError(xhr) {
        const errors = xhr.responseJSON.errors;
        if (errors && errors.semester) {
            showError($semester, errors.semester[0]);
        } else {
            showErrorAlert("Terjadi kesalahan!");
            console.log(xhr.responseText);
        }
    }

    function showSuccessAlert(message) {
        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: message,
            showConfirmButton: false,
            timer: 1500,
        });
    }

    function showErrorAlert(message) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: message,
        });
    }

    function refreshTable() {
        $("#major-subject-table").DataTable().ajax.reload();
    }
});
