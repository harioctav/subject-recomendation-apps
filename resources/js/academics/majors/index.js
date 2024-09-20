import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $(".table").DataTable();

    $("#degree").on("change", function (e) {
        table.draw();
        e.preventDefault();
    });
});

function deleteRole(url) {
    showConfirmationModal(
        "Dengan menekan tombol hapus, Maka semua data <b>Jurusan</b> tersebut akan hilang!",
        "Hapus Data",
        url,
        "DELETE",
        handleSuccess
    );
}

function handleSuccess() {
    table.ajax.reload();
}

$(document).on("click", ".delete-majors", function (e) {
    e.preventDefault();
    let url = urlDestroy;
    url = url.replace(":uuid", $(this).data("uuid"));
    deleteRole(url);
});

$(document).on("click", ".show-majors", function (e) {
    e.preventDefault();
    let url = urlShow;
    url = url.replace(":uuid", $(this).data("uuid"));
    show(url);
});

function show(url) {
    const modal = $("#modal-show-major");
    const modalContent = modal.find(".modal-content");

    modal.modal("show");
    modal.find(".block-title").text("Detail Data Jurusan");

    $.get(url).done((response) => {
        const major = response;

        const majorElements = {
            "#major-code": major.code,
            "#major-name": major.name,
            "#major-degree": major.degree,
            "#major-total-course-credit": major.total_course_credit || "---",
        };

        Object.entries(majorElements).forEach(([selector, value]) => {
            modalContent.find(selector).text(value);
        });
    });
}
