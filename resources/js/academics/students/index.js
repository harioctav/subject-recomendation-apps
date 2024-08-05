import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $(".table").DataTable();

    $("#status").on("change", function (e) {
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

function handleSuccess() {
    table.ajax.reload();
}

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
