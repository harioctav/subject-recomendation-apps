import { showSwalWarning, showSwalError } from "@/utils/helper.js";

$(document).ready(function () {
    var selectedCourses = new Set();

    var table = $("#coursesTable").DataTable({
        ajax: {
            url: datatableURL,
            dataSrc: "",
        },
        pageLength: 5,
        lengthMenu: [
            [5, 10, 25, -1],
            [5, 10, 25, "All"],
        ],
        responsive: true,
        columns: [
            {
                data: null,
                orderable: false,
                className: "text-center",
                render: function (data, type, row) {
                    var checked = selectedCourses.has(row.id.toString())
                        ? "checked"
                        : "";
                    return `<input type="checkbox" class="course-checkbox" value="${row.id}" ${checked}>`;
                },
            },
            {
                data: "semester",
                className: "text-center",
                orderable: false,
            },
            {
                data: "subject_name",
                className: "text-center",
                orderable: false,
            },
            {
                data: "sks",
                className: "text-center",
                orderable: false,
            },
            {
                data: "note",
                className: "text-center",
                orderable: false,
            },
            {
                data: "status",
                className: "text-center",
                orderable: false,
            },
        ],
        rowGroup: {
            dataSrc: "semester",
        },
        drawCallback: function () {
            $(".course-checkbox").each(function () {
                if (selectedCourses.has($(this).val())) {
                    $(this).prop("checked", true);
                }
            });
        },
    });

    // Handle "Select All" checkbox
    $("#select-all").on("click", function () {
        var allChecked = this.checked;
        table
            .rows({ page: "current" })
            .nodes()
            .each(function (node) {
                var checkbox = $(node).find(".course-checkbox");
                checkbox.prop("checked", allChecked);
                if (allChecked) {
                    selectedCourses.add(checkbox.val());
                } else {
                    selectedCourses.delete(checkbox.val());
                }
            });
    });

    // Handle individual checkbox changes
    $("#coursesTable").on("change", ".course-checkbox", function () {
        if (this.checked) {
            selectedCourses.add(this.value);
        } else {
            selectedCourses.delete(this.value);
        }

        if (
            $(".course-checkbox:checked").length ===
            table.rows({ page: "current" }).count()
        ) {
            $("#select-all").prop("checked", true);
        } else {
            $("#select-all").prop("checked", false);
        }
    });

    // Handle form submission
    $("form").on("submit", function (e) {
        e.preventDefault();

        // Clear existing hidden inputs
        $('input[name="courses[]"]').remove();

        // Add hidden inputs for selected courses
        selectedCourses.forEach(function (courseId) {
            $("<input>")
                .attr({
                    type: "hidden",
                    name: "courses[]",
                    value: courseId,
                })
                .appendTo("form");
        });

        // Submit the form
        this.submit();
    });
});
