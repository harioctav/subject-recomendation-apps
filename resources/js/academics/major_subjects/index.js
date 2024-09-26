import { showConfirmationModal } from "@/utils/helper.js";

let table;

$(() => {
    table = $("#major-subject-table").DataTable();

    initializeDeleteButtons();
});

function deleteMajorSubject(url) {
    showConfirmationModal(
        "Dengan menekan tombol hapus, Maka data <b>Mata Kuliah</b> tersebut akan dihapus dari Program Studi ini!",
        "Hapus Data",
        url,
        "DELETE",
        handleSuccess
    );
}

function handleSuccess() {
    table.ajax.reload();
}

function initializeDeleteButtons() {
    $(document).on("click", ".delete-major-subjects", function (e) {
        e.preventDefault();
        const deleteUrl = $(this).data("delete-url");
        if (deleteUrl) {
            deleteMajorSubject(deleteUrl);
        } else {
            console.error("Delete URL not found");
        }
    });
}
