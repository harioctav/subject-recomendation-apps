import { showSwalWarning, showSwalError } from "@/utils/helper.js";

$(document).ready(function () {
    function updateSelectBoxContent(semestersData) {
        const selectBox = $("#example-select2-multiple");
        selectBox.empty();

        if (!semestersData || semestersData.length === 0) {
            selectBox.append(
                "<option>Tidak ada mata kuliah yang tersedia untuk direkomendasikan saat ini.</option>"
            );
            return;
        }

        semestersData.forEach(function (semesterData) {
            const optgroup = $("<optgroup>").attr(
                "label",
                `Semester ${semesterData.semester}`
            );

            semesterData.subjects.forEach(function (subject) {
                const option = $("<option>")
                    .val(subject.id)
                    .attr("data-sks", subject.sks)
                    .text(subject.subject_name);
                optgroup.append(option);
            });

            selectBox.append(optgroup);
        });

        selectBox.trigger("change");
    }

    function updateStudentDetails(studentId) {
        let url = studentDetailURL.replace(":student_id", studentId);
        $.ajax({
            url: url,
            method: "GET",
            success: function (data) {
                $("#student-nim").text(data.nim);
                $("#student-major").text(data.major_name);
                $("#student-total-course-credit").text(
                    data.total_course_credit
                );
                $("#student-total-course-credit-done").text(
                    data.total_course_credit_done
                );
                $("#student-total-course-credit-remainder").text(
                    data.total_course_credit_remainder
                );
                $("#student-status").text(data.status);
            },
            error: function () {
                $("#student-nim").text("-");
                $("#student-major").text("-");
                $("#student-total-course-credit").text("-");
                $("#student-total-course-credit-done").text("-");
                $("#student-total-course-credit-remainder").text("-");
                $("#student-status").text("-");
            },
        });
    }

    function toggleSelectBoxVisibility(studentId) {
        if (studentId) {
            $(".select-box-container").show();
            $(".loading-indicator").show();
            updateStudentDetails(studentId);

            let url = studentURL.replace(":student_id", studentId);

            $.ajax({
                url: url,
                method: "GET",
                success: function (data) {
                    updateSelectBoxContent(data);
                },
                error: function (xhr) {
                    if (xhr.status === 404) {
                        $(".select-box-container").html(
                            "<p>Tidak ada mata kuliah yang tersedia untuk direkomendasikan saat ini.</p>"
                        );
                    } else {
                        $(".select-box-container").html(
                            "<p>Terjadi kesalahan. Silakan coba lagi.</p>"
                        );
                    }
                },
                complete: function () {
                    $(".loading-indicator").hide();
                },
            });
        } else {
            $(".select-box-container").hide();
            $("#student-nim").text("-");
            $("#student-major").text("-");
            $("#student-total-course-credit").text("-");
            $("#student-total-course-credit-done").text("-");
            $("#student-total-course-credit-remainder").text("-");
            $("#student-status").text("-");
        }
    }

    function checkSKSLimit(totalSKS) {
        const maxSKS = 24;
        if (totalSKS > maxSKS) {
            showSwalWarning(
                "Peringatan",
                "Diharapkan tidak memilih SKS dengan total lebih dari 24 SKS. Silahkan hapus salah satu Matakuliah untuk mengurangi jumlah SKS!!"
            );
            $('button[type="submit"]').prop("disabled", true);
        } else {
            $('button[type="submit"]').prop("disabled", false);
        }
    }

    function updateTotalSKS() {
        let totalSKS = 0;
        $("#example-select2-multiple option:selected").each(function () {
            let sks = $(this).data("sks");
            if (!isNaN(sks)) {
                totalSKS += parseInt(sks, 10);
            }
        });
        $("#sks").val(totalSKS);
        checkSKSLimit(totalSKS);
    }

    $("#student_id").on("change", function () {
        let studentId = $(this).val();
        toggleSelectBoxVisibility(studentId);
    });

    $("#example-select2-multiple").on("change", function () {
        updateTotalSKS();
    });

    toggleSelectBoxVisibility($("#student_id").val());

    $("form").on("submit", function (e) {
        let totalSKS = parseInt($("#sks").val(), 10);
        if ($("#example-select2-multiple option:selected").length === 0) {
            e.preventDefault();
            showSwalError("Pilih Setidaknya Satu Matakuliah");
        } else if (totalSKS > 24) {
            e.preventDefault();
            showSwalWarning(
                "Peringatan",
                "Diharapkan tidak memilih SKS dengan total lebih dari 24 SKS. Silahkan hapus salah satu Matakuliah untuk mengurangi jumlah SKS!!"
            );
        }
    });
});
