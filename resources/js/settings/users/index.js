import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $(".table").DataTable();

    $("#status").on("change", function (e) {
        table.draw();
        e.preventDefault();
    });

    $("#roles").on("change", function (e) {
        table.draw();
        e.preventDefault();
    });
});

function deleteUser(url) {
    showConfirmationModal(
        "Dengan menekan tombol hapus, Maka semua data <b>Pengguna</b> tersebut akan hilang!",
        "Hapus Data",
        url,
        "DELETE",
        handleSuccess
    );
}

$(document).on("click", ".status-users", function (e) {
    e.preventDefault();
    let url = urlStatus;
    url = url.replace(":uuid", $(this).data("uuid"));
    showConfirmationModal(
        "Dengan menekan tombol <b>Ubah Status</b>, Maka <b>Status Keaktifan Pengguna</b> akan berubah dan Pengguna tidak dapat melakukan masuk ke dalam sistem",
        "Ubah Status Pengguna",
        url,
        "PATCH",
        handleSuccess
    );
});

function handleSuccess() {
    table.ajax.reload();
}

$(document).on("click", ".delete-users", function (e) {
    e.preventDefault();
    let url = urlDestroy;
    url = url.replace(":uuid", $(this).data("uuid"));
    deleteUser(url);
});
