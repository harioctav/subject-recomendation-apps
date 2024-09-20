import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $("#student-table").DataTable();

    $("#status").on("change", function () {
        table.draw();
    });
});

function deleteRole(url) {
    showConfirmationModal(
        "Jika matakuliah pada Data Rekomendasi di sini sudah diberi penilaian, Maka data tidak akan bisa dilakukan penghapusan!!",
        "Hapus Data",
        url,
        "DELETE",
        handleSuccess
    );
}

function handleSuccess() {
    table.ajax.reload();
}

$(document).on("click", ".delete-recommendations", function (e) {
    e.preventDefault();
    let url = urlDestroy;
    url = url.replace(":uuid", $(this).data("uuid"));
    deleteRole(url);
});
