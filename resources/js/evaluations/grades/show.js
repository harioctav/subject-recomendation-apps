import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $("#grade-table").DataTable();

    $("#grade").on("change", function () {
        table.draw();
        e.preventDefault();
    });

    $(document).on("click", ".delete-grades", function (e) {
        e.preventDefault();
        let url = urlDestroy.replace(":uuid", $(this).data("uuid"));
        deleteGrade(url);
    });
});

function deleteGrade(url) {
    showConfirmationModal(
        "Dengan menekan tombol hapus, Maka semua data <b>Nilai</b> tersebut akan hilang!",
        "Hapus Data",
        url,
        "DELETE",
        handleSuccess
    );
}

function handleSuccess() {
    table.ajax.reload();
}
