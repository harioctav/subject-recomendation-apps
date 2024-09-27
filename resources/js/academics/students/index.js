import {
    showConfirmationModal,
    mapGender,
    formatDate,
    mapReligion,
    mapStudentStatus,
    ucFirst,
} from "@/utils/helper.js";

let table;

$(() => {
    table = $(".table").DataTable();

    $("#status").on("change", function (e) {
        table.draw();
        e.preventDefault();
    });

    $("#student-status").on("change", function (e) {
        table.draw();
        e.preventDefault();
    });

    $("#isTrash-switch").on("change", function (e) {
        table.draw();
        e.preventDefault();
    });
});

function deleteStudent(url) {
    showConfirmationModal(
        "Dengan menekan tombol hapus, Maka semua data <b>Mahasiswa</b> tersebut akan hilang!",
        "Hapus Data",
        url,
        "DELETE",
        handleSuccess
    );
}

const getModalStudent = async (url) => {
    try {
        $("#modal-show-student").modal("show");

        const response = await $.get(url);
        const student = response.student;

        const village = student.village;
        const district = student.village?.district;
        const regency = student.village?.district?.regency;
        const province = student.village?.district?.regency?.province;

        const avatarUrl =
            student.avatar || "/assets/images/placeholders/default-avatar.png";
        $("#modal-show-student .student-avatar").attr("src", avatarUrl);

        const modalElements = {
            ".student-name": student.name,
            ".student-nim": student.nim,
            ".student-major-name": student.major?.name,
            ".student-upbjj": student.upbjj || "-",
            ".student-initial-registration-period":
                student.initial_registration_period || "-",
            ".student-origin-department": student.origin_department || "-",
            ".student-gender": mapGender(student.gender) || "-",
            ".student-birth-place": student.birth_place || "-",
            ".student-birth-day": formatDate(student.birth_date) || "-",
            ".student-religion": mapReligion(student.religion) || "-",
            ".student-status": mapStudentStatus(student.student_status),
            ".student-status-regis": ucFirst(student.status),
            ".student-phone": student.phone || "-",
            ".student-email": student.email || "-",
            ".student-parent-name": student.parent_name || "-",
            ".student-parent-phone": student.parent_phone_number || "-",
            ".student-address": student.address || "-",

            ".student-province": province?.name || "-",
            ".student-regency": regency?.name || "-",
            ".student-district": district?.name || "-",
            ".student-village": village?.name || "-",
            ".student-postal-code": village?.pos_code || "-",
        };

        Object.entries(modalElements).forEach(([selector, value]) => {
            $(`#modal-show-student ${selector}`).text(value);
        });
    } catch (error) {
        console.error("Error fetching student data:", error);
    }
};

function handleSuccess() {
    table.ajax.reload();
}

$(document).on("click", ".show-students", function (e) {
    e.preventDefault();
    let url = urlDetailed;
    url = url.replace(":uuid", $(this).data("uuid"));
    getModalStudent(url);
});

$(document).on("click", ".delete-students", function (e) {
    e.preventDefault();
    let url = urlDestroy;
    url = url.replace(":uuid", $(this).data("uuid"));
    deleteStudent(url);
});

$(document).on("click", ".restore-students", function (e) {
    e.preventDefault();
    let url = urlRestore.replace(":uuid", $(this).data("uuid"));
    showConfirmationModal(
        "Dengan menekan tombol restore, Maka data <b>Mahasiswa</b> tersebut akan dikembalikan!",
        "Restore Data",
        url,
        "PUT",
        handleSuccess
    );
});

$(document).on("click", ".delete-permanent-students", function (e) {
    e.preventDefault();
    let uuid = $(this).data("uuid");
    let url = urlForceDelete.replace(":uuid", uuid);

    showConfirmationModal(
        "Dengan menekan tombol hapus permanen, Maka data <b>Mahasiswa</b> tersebut akan hilang selamanya!",
        "Hapus Permanen",
        url,
        "DELETE",
        handleSuccess
    );
});
