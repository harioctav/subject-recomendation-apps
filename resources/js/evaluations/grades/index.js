let table;

$(() => {
    table = $("#student-table").DataTable();

    $("#status").on("change", function () {
        table.draw();
    });
});
