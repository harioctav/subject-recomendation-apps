$(document).ready(function () {
    var selectedCourses = new Set();
    var table;

    function initializeDataTable(data) {
        if (table) {
            table.destroy();
        }

        table = $("#coursesTable").DataTable({
            data: data,
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
                    data: "subject_code",
                    className: "text-center",
                    orderable: false,
                },
                {
                    data: "subject_name",
                    className: "text-center",
                    orderable: false,
                },
                {
                    data: "grade",
                    className: "text-center",
                    orderable: false,
                },
                {
                    data: "sks",
                    className: "text-center",
                    orderable: false,
                },
                {
                    data: "note_subject",
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
            rowCallback: function (row, data) {
                if (data.grade === "E") {
                    $(row).css("background-color", "red");
                    $(row).css("color", "white"); // Optional: to make text readable
                }
            },
            drawCallback: function () {
                $(".course-checkbox").each(function () {
                    if (selectedCourses.has($(this).val())) {
                        $(this).prop("checked", true);
                    }
                });
                updateSelectedSKS();
            },
        });
    }

    function loadCourses(sks = "") {
        $.ajax({
            url: datatableURL,
            data: { sks: sks },
            success: function (data) {
                initializeDataTable(data);
            },
            error: function (xhr, status, error) {
                showSwalError("Error loading courses");
            },
        });
    }

    function calculateTotalSKS() {
        let totalSKS = 0;
        selectedCourses.forEach(function (courseId) {
            let row = table
                .rows()
                .data()
                .toArray()
                .find((r) => r.id.toString() === courseId);
            if (row) {
                totalSKS += parseInt(row.sks);
            }
        });
        return totalSKS;
    }

    function updateSelectedSKS() {
        let totalSKS = calculateTotalSKS();
        $("#course_credit_selected").val(totalSKS);

        if (totalSKS > 24) {
            $("#sks-error-message").removeClass("d-none");
            $("#button-submit").prop("disabled", true);
        } else {
            $("#sks-error-message").addClass("d-none");
            $("#button-submit").prop("disabled", false);
        }
    }

    function updateSelectedCourses(checkbox) {
        if (checkbox.checked) {
            selectedCourses.add(checkbox.value);
        } else {
            selectedCourses.delete(checkbox.value);
        }
        updateSelectedSKS();
    }

    // Initial load
    loadCourses();

    // Handle SKS filter
    $("#course_credit").on("input", function () {
        var sks = $(this).val().trim();
        if (sks === "") {
            loadCourses();
        } else if (
            isNaN(parseInt(sks)) ||
            parseInt(sks) < 1 ||
            parseInt(sks) > 24
        ) {
            showSwalWarning("SKS harus berupa angka antara 1 dan 24");
        } else {
            loadCourses(sks);
        }
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
        updateSelectedSKS();
    });

    // Handle individual checkbox changes
    $("#coursesTable").on("change", ".course-checkbox", function () {
        updateSelectedCourses(this);

        let totalSKS = calculateTotalSKS();
        if (totalSKS > 24) {
            showSwalWarning(
                "Total SKS tidak boleh melebihi 24. Mohon kurangi pilihan mata kuliah."
            );
            $(this).prop("checked", false);
            selectedCourses.delete(this.value);
            updateSelectedSKS();
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

        if (selectedCourses.size === 0) {
            showSwalWarning("Anda harus memilih setidaknya 1 Matakuliah.");
            return;
        }

        let totalSKS = calculateTotalSKS();
        if (totalSKS > 24) {
            showSwalWarning(
                "Total SKS tidak boleh melebihi 24. Mohon kurangi pilihan Matakuliah."
            );
            return;
        }

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
        disableSubmitButton();
        this.submit();
    });

    // Function to disable submit button
    function disableSubmitButton() {
        $("#button-submit").prop("disabled", true);
        return true;
    }
});
