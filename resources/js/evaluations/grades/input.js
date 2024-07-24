$(() => {
    // Papulate function
    function populateStudentDropdown(selector, options, placeholder) {
        const dropdown = $(selector);
        dropdown.empty().append(`<option value="">${placeholder}</option>`);

        $.each(options, function (key, value) {
            dropdown.append(
                `<option value="${value.id}" data-uuid="${value.uuid}">${value.name}</option>`
            );
        });
    }

    function populateSubjectDropdown(selector, groupedOptions, placeholder) {
        const dropdown = $(selector);
        dropdown.empty().append(`<option value="">${placeholder}</option>`);

        $.each(groupedOptions, function (semester, subjects) {
            const optgroup = $("<optgroup>").attr(
                "label",
                `Semester ${semester}`
            );

            $.each(subjects, function (key, subject) {
                optgroup.append(
                    `<option value="${subject.id}" data-uuid="${subject.uuid}">${subject.name}</option>`
                );
            });

            dropdown.append(optgroup);
        });
    }

    // loadValues();

    // function loadValues() {
    //     const oldMajorId = $("#major_id").data("old");
    //     const oldStudentId = $("#student_id").data("old");
    //     const oldSubjectId = $("#subject_id").data("old");

    //     // Menyimpan UUID dari major yang sudah terpilih
    //     const majorOption = $("#major_id").find(
    //         `option[value="${oldMajorId}"]`
    //     );
    //     const oldMajorUuid = majorOption.data("uuid");

    //     // Memperbarui dropdown major_id
    //     if (oldMajorId) {
    //         $("#major_id").val(oldMajorId).trigger("change");

    //         // Memuat data mahasiswa jika ada major yang sudah terpilih
    //         if (oldMajorUuid) {
    //             const url = studentURL.replace(":major", oldMajorUuid);
    //             $.ajax({
    //                 url: url,
    //                 type: "GET",
    //                 dataType: "json",
    //                 success: function (response) {
    //                     populateStudentDropdown(
    //                         "#student_id",
    //                         response,
    //                         "Pilih Mahasiswa"
    //                     );

    //                     // Memilih student_id jika ada
    //                     if (oldStudentId) {
    //                         $("#student_id")
    //                             .val(oldStudentId)
    //                             .trigger("change");
    //                     }
    //                 },
    //                 error: function () {
    //                     populateStudentDropdown(
    //                         "#student_id",
    //                         [],
    //                         "Pilih Mahasiswa"
    //                     );
    //                 },
    //             });
    //         }
    //     }
    // }

    $("#major_id").on("change", function () {
        const majorId = $(this).val();

        if (majorId) {
            const url = studentURL.replace(":major_id", majorId);

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    populateStudentDropdown(
                        "#student_id",
                        response,
                        "Pilih Mahasiswa"
                    );
                },
                error: function () {
                    populateStudentDropdown(
                        "#student_id",
                        [],
                        "Pilih Mahasiswa"
                    );
                },
            });
        } else {
            populateStudentDropdown("#student_id", [], "Pilih Mahasiswa");
        }
    });

    $("#student_id").on("change", function () {
        const selectedOption = $(this).find("option:selected");
        const uuid = selectedOption.data("uuid");

        if (uuid) {
            const url = subjectURL.replace(":student", uuid);

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    populateSubjectDropdown(
                        "#subject_id",
                        response, // Data yang dikelompokkan berdasarkan semester
                        "Pilih Matakuliah"
                    );
                },
                error: function () {
                    populateSubjectDropdown(
                        "#subject_id",
                        {},
                        "Pilih Matakuliah"
                    );
                },
            });
        } else {
            populateSubjectDropdown("#subject_id", {}, "Pilih Matakuliah");
        }
    });
});
