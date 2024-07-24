$(() => {
    function populateDropdown(selector, groups, placeholder) {
        const dropdown = $(selector);
        dropdown.empty().append(`<option value="">${placeholder}</option>`);

        $.each(groups, function (key, group) {
            const optgroup = $("<optgroup>").attr(
                "label",
                "Semester " + group.semester
            );
            $.each(group.subjects, function (key, subject) {
                optgroup.append(
                    `<option value="${subject.id}" data-uuid="${subject.uuid}">${subject.name}</option>`
                );
            });
            dropdown.append(optgroup);
        });

        const oldSubjectId = dropdown.data("old");
        if (oldSubjectId) {
            dropdown.val(oldSubjectId).trigger("change");
        }
    }

    function loadStudentData(uuid) {
        if (uuid) {
            let url = studentURL.replace(":student", uuid);
            $.ajax({
                url: url,
                type: "GET",
                success: function (data) {
                    $("#student-nim").text(data.student.nim);
                    $("#student-major").text(data.student.major);

                    var subjectsGroupedBySemester = data.subjects.map(function (
                        group
                    ) {
                        return {
                            semester: group.semester,
                            subjects: group.subjects,
                        };
                    });

                    populateDropdown(
                        "#subject_id",
                        subjectsGroupedBySemester,
                        "Pilih Matakuliah"
                    );

                    // Setel nilai subject_id jika ada nilai lama
                    const oldSubjectId = $("#subject_id").data("old");
                    if (oldSubjectId) {
                        $("#subject_id").val(oldSubjectId).trigger("change");
                    }
                },
                error: function (xhr) {
                    $("#student-nim").text("--");
                    $("#student-major").text("--");
                    $("#subject_id").empty().append("<option></option>");
                },
            });
        } else {
            $("#student-nim").text("--");
            $("#student-major").text("--");
            $("#subject_id").empty().append("<option></option>");
        }
    }

    // Ketika halaman dimuat, tangani mahasiswa yang dipilih sebelumnya
    const oldStudentId = $("#student_id").data("old");
    if (oldStudentId) {
        $("#student_id").val(oldStudentId).trigger("change");
        const selectedUuid = $("#student_id option:selected").data("uuid");
        loadStudentData(selectedUuid);
    }

    // Tangani perubahan mahasiswa
    $("#student_id").on("change", function () {
        var selectedOption = $(this).find("option:selected");
        var uuid = selectedOption.data("uuid");
        loadStudentData(uuid);
    });
});
