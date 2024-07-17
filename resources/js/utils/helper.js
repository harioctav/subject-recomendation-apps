export function showConfirmationModal(
    message,
    confirmButtonText,
    url,
    method,
    onSuccess
) {
    Swal.fire({
        icon: "warning",
        title: "Apakah Anda Yakin?",
        html: message,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: "Batalkan",
        cancelButtonColor: "#E74C3C",
        confirmButtonColor: "#3498DB",
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: url,
                type: method,
                data: {
                    _token: $("[name=csrf-token]").attr("content"),
                    _method: method,
                },
                success: function (response) {
                    Swal.fire({
                        icon: "success",
                        title: response.message,
                        confirmButtonText: "Selesai",
                    });

                    if (onSuccess) {
                        onSuccess();
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        // title: xhr.responseJSON.message,
                        title: "Tidak bisa melakukan Aksi, Data tidak valid untuk aksi ini",
                        confirmButtonText: "Mengerti",
                    });
                    return;
                },
            });
        } else if (result.dismiss == swal.DismissReason.cancel) {
            Swal.fire({
                icon: "error",
                title: "Tidak ada perubahan disimpan",
                confirmButtonText: "Mengerti",
                confirmButtonColor: "#3498DB",
            });
        }
    });
}

export function processPermission(permissionName) {
    const parts = permissionName.split(".");
    const leftPart = parts[0];
    const rightPart = parts[1];
    const translatedName = translations[leftPart][rightPart];
    return translatedName;
}

export function initializeFlatpickr(selector) {
    var dateInput = $(selector);

    dateInput.flatpickr({
        minDate: "today",
        dateFormat: "Y-m-d",
    });
}
