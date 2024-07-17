import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $(".table").DataTable();
});

function deleteRole(url) {
    showConfirmationModal(
        "Dengan menekan tombol hapus, Maka semua data <b>PENGGUNA</b> pada role tersebut akan hilang!",
        "Hapus Data",
        url,
        "DELETE",
        handleSuccess
    );
}

function handleSuccess() {
    table.ajax.reload();
}

$(document).on("click", ".delete-roles", function (e) {
    e.preventDefault();
    let url = urlDestroy;
    url = url.replace(":uuid", $(this).data("uuid"));
    deleteRole(url);
});
