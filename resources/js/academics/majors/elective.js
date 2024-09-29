$(document).ready(function () {
    $("#major-elective-subjects-table").DataTable({
        ajax: {
            url: urlElectiveTable, // Sesuaikan dengan endpoint API Anda
            dataSrc: "",
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                class: "text-center",
            },
            {
                data: "code",
                class: "text-center",
            },
            {
                data: "name",
                class: "text-center",
            },
            {
                data: "course_credit",
                class: "text-center",
            },
        ],
        ordering: false,
        info: false,
        lengthMenu: [
            [2, 5, 15, -1],
            [2, 5, 15, "All"],
        ],
        drawCallback: function (settings) {
            var api = this.api();
            var rows = api.rows({ page: "current" }).nodes();
            var last = null;
            var index = 1;

            api.column(0, { page: "current" })
                .data()
                .each(function (group, i) {
                    var data = api.row(api.row($(rows).eq(i)).index()).data();
                    var semester = data["semester"];

                    if (last !== semester) {
                        $(rows)
                            .eq(i)
                            .before(
                                '<tr class="semester-header">' +
                                    '<td colspan="4">SEMESTER ' +
                                    semester +
                                    "</td>" +
                                    "</tr>"
                            );
                        index = 1;
                    }

                    api.cell(rows[i], 0).data(index);

                    index++;
                    last = semester;
                });
        },
    });
});
