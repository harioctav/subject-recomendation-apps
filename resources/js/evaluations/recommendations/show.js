import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $("#recommendation-table").DataTable();

    $("#note").on("change", function () {
        table.draw();
    });

    $("#semester").on("change", function () {
        table.draw();
    });
});

function deleteRecommendation(url) {
    showConfirmationModal(
        "Dengan menekan tombol hapus, Maka semua data yang sudah <b>Rekomendasikan</b> tersebut akan hilang!",
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
    deleteRecommendation(url);
});
