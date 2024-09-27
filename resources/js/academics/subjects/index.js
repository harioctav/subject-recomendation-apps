import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $(".table").DataTable();
});

function deleteRole(url) {
    showConfirmationModal(
        "Dengan menekan tombol hapus, Maka semua data <b>Matakuliah</b> tersebut akan hilang!",
        "Hapus Data",
        url,
        "DELETE",
        handleSuccess
    );
}

const getModalSubject = async (url) => {
    try {
        $("#modal-show-subject").modal("show");

        const response = await $.get(url);
        const modalElements = {
            "#subject-code": response.code,
            "#subject-name": response.name,
            "#subject-course-credit": response.course_credit,
            "#subject-exam-time": response.exam_time,
            "#subject-status": response.status,
            "#subject-note": response.note,
        };

        Object.entries(modalElements).forEach(([selector, value]) => {
            $(`#modal-show-subject ${selector}`).text(value);
        });
    } catch (error) {
        console.error("Error fetching subject data:", error);
    }
};

function handleSuccess() {
    table.ajax.reload();
}

$(document).on("click", ".delete-subjects", function (e) {
    e.preventDefault();
    let url = urlDestroy;
    url = url.replace(":uuid", $(this).data("uuid"));
    deleteRole(url);
});

$(document).on("click", ".show-subjects", function (e) {
    e.preventDefault();
    let url = urlDetail;
    url = url.replace(":uuid", $(this).data("uuid"));
    getModalSubject(url);
});
