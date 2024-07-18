import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $(".table").DataTable();
});

function deleteRole(url) {
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
    deleteRole(url);
});
