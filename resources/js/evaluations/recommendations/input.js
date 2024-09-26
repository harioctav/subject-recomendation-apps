// Array untuk menyimpan subject yang dipilih
let selectedSubjects = [];
let totalSKS = 0;
const MAX_SKS = 24;

$(document).ready(function () {
    let table = $("#subject-table").DataTable();

    function updateTotalSKS() {
        $("#course_credit_selected").val(totalSKS);
        let creditLimit = MAX_SKS;
        if (totalSKS >= creditLimit) {
            $(".select-subject-checkbox:not(:checked)").prop("disabled", true);
        } else {
            $(".select-subject-checkbox").prop("disabled", false);
        }
    }

    // Reset checkboxes on page load
    $(".select-subject-checkbox").prop("checked", false);
    $("#select-all").prop("checked", false);
    selectedSubjects = [];

    $("#select-all").on("change", function () {
        if ($(this).is(":checked") && totalSKS < MAX_SKS) {
            $(".select-subject-checkbox:not(:checked)").each(function () {
                let sks = parseInt($(this).data("sks"));
                if (totalSKS + sks <= MAX_SKS) {
                    $(this).prop("checked", true);
                    selectedSubjects.push($(this).val());
                    totalSKS += sks;
                }
            });
        } else {
            $(".select-subject-checkbox:checked").each(function () {
                $(this).prop("checked", false);
                selectedSubjects = selectedSubjects.filter(
                    (id) => id != $(this).val()
                );
                totalSKS -= parseInt($(this).data("sks"));
            });
        }
        updateTotalSKS();
    });

    $("#subject-table tbody").on(
        "change",
        ".select-subject-checkbox",
        function () {
            let subjectId = $(this).val();
            let sks = parseInt($(this).data("sks"));

            if ($(this).is(":checked")) {
                if (totalSKS + sks <= MAX_SKS) {
                    if (!selectedSubjects.includes(subjectId)) {
                        selectedSubjects.push(subjectId);
                        totalSKS += sks;
                    }
                } else {
                    $(this).prop("checked", false);
                    alert("Maksimal SKS yang bisa dipilih adalah 24");
                }
            } else {
                selectedSubjects = selectedSubjects.filter(
                    (id) => id != subjectId
                );
                totalSKS -= sks;
            }

            updateTotalSKS();
        }
    );

    table.on("draw", function () {
        $(".select-subject-checkbox").each(function () {
            let subjectId = $(this).val();
            $(this).prop("checked", selectedSubjects.includes(subjectId));
        });
        updateTotalSKS();
    });

    $(document).ready(function () {
        let table = $("#subject-table").DataTable();

        $("#grade_filter").on("change", function () {
            table.draw();
        });

        $("#search_custom").on("keyup", function () {
            table.draw();
        });
    });
});
