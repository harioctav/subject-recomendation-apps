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
                        title:
                            xhr.responseJSON.message ||
                            "Tidak bisa melakukan Aksi, Data tidak valid untuk aksi ini",
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

export const mapGender = (gender) => {
    const genderMap = {
        male: "Laki-laki",
        female: "Perempuan",
        unknown: "Tidak Diketahui",
    };
    return genderMap[gender] || gender;
};

export const mapReligion = (religion) => {
    const religionMap = {
        islam: "Islam",
        kristen: "Kristen",
        hindu: "Hindu",
        buddha: "Budhha",
        katolik: "Katolik",
        unknown: "Tidak Diketahui",
    };
    return religionMap[religion] || religion;
};

export const mapStudentStatus = (status) => {
    const statusMap = {
        1: "Aktif",
        0: "Non Aktif",
    };
    return statusMap[status] || "Tidak Diketahui";
};

export const ucFirst = (string) => {
    if (string === "unknown") return "Tidak Diketahui";
    else return string.charAt(0).toUpperCase() + string.slice(1);
};

export function formatDate(dateString) {
    if (dateString === "" || !dateString) {
        return "-";
    }
    const date = moment(dateString, "YYYY-MM-DD", true);
    if (!date.isValid()) {
        return "-";
    }

    const days = [
        "Minggu",
        "Senin",
        "Selasa",
        "Rabu",
        "Kamis",
        "Jumat",
        "Sabtu",
    ];
    const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];

    return `${days[date.day()]}, ${date.date()} ${
        months[date.month()]
    } ${date.year()}`;
}

export const showSwalWarning = (title, text) => {
    return Swal.fire({
        icon: "warning",
        title: title,
        text: text,
        confirmButtonText: "Mengerti",
    });
};

export const showSwalError = (title, text) => {
    return Swal.fire({
        icon: "error",
        title: title,
        text: text,
        confirmButtonText: "Mengerti",
    });
};

export const showSwalSuccess = (title, text) => {
    return Swal.fire({
        icon: "success",
        title: title,
        text: text,
        confirmButtonText: "Mengerti",
    });
};
