$(() => {
    var today = new Date();
    flatpickr("#brith_day", {
        dateFormat: "Y-m-d",
        maxDate: today,
    });

    // Function to load old values
    function loadOldValues() {
        const oldProvince = $("#province").data("old");
        const oldRegency = $("#regency").data("old");
        const oldDistrict = $("#district").data("old");
        const oldVillage = $("#village").data("old");
        const oldPostCode = $("#post_code").data("old");

        if (oldProvince) {
            $("#province").val(oldProvince).trigger("change");
        }

        // Load regency if old value exists
        if (oldRegency) {
            let url = regencies_url.replace(":province_id", oldProvince);
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $("#regency")
                        .empty()
                        .append('<option value="">Pilih Kabupaten</option>');
                    $.each(data, function (key, value) {
                        $("#regency").append(
                            `<option value="${value.id}" ${
                                value.id == oldRegency ? "selected" : ""
                            }>${value.type} ${value.name}</option>`
                        );
                    });
                    $("#regency").trigger("change");

                    // Load district if old value exists
                    if (oldDistrict) {
                        let url = districts_url.replace(
                            ":regency_id",
                            oldRegency
                        );
                        $.ajax({
                            url: url,
                            type: "GET",
                            dataType: "json",
                            success: function (data) {
                                $("#district")
                                    .empty()
                                    .append(
                                        '<option value="">Pilih Kecamatan</option>'
                                    );
                                $.each(data, function (key, value) {
                                    $("#district").append(
                                        `<option value="${value.id}" ${
                                            value.id == oldDistrict
                                                ? "selected"
                                                : ""
                                        }>${value.name}</option>`
                                    );
                                });
                                $("#district").trigger("change");

                                // Load village if old value exists
                                if (oldVillage) {
                                    let url = villages_url.replace(
                                        ":district_id",
                                        oldDistrict
                                    );
                                    $.ajax({
                                        url: url,
                                        type: "GET",
                                        dataType: "json",
                                        success: function (data) {
                                            $("#village")
                                                .empty()
                                                .append(
                                                    '<option value="">Pilih Kelurahan</option>'
                                                );
                                            $.each(data, function (key, value) {
                                                $("#village").append(
                                                    `<option value="${
                                                        value.id
                                                    }" ${
                                                        value.id == oldVillage
                                                            ? "selected"
                                                            : ""
                                                    }>${value.name}</option>`
                                                );
                                            });
                                            $("#village").trigger("change");

                                            // Set post_code if old value exists
                                            if (oldPostCode) {
                                                $("#post_code").val(
                                                    oldPostCode
                                                );
                                            }
                                        },
                                    });
                                }
                            },
                        });
                    }
                },
            });
        }
    }

    // Call the function to load old values
    loadOldValues();

    console.log(loadOldValues());

    // Event handlers for dropdown changes
    $("#province").on("change", function () {
        var province_id = $(this).val();
        let url = regencies_url.replace(":province_id", province_id);

        if (province_id) {
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $("#regency")
                        .empty()
                        .append('<option value="">Pilih Kabupaten</option>');
                    $.each(data, function (key, value) {
                        $("#regency").append(
                            `<option value="${value.id}">${value.type} ${value.name}</option>`
                        );
                    });
                },
            });
        } else {
            $("#regency")
                .empty()
                .append('<option value="">Pilih Kabupaten</option>');
        }
        $("#district")
            .empty()
            .append('<option value="">Pilih Kecamatan</option>');
        $("#village")
            .empty()
            .append('<option value="">Pilih Kelurahan</option>');
        $("#post_code").val("");
    });

    $("#regency").on("change", function () {
        var regency_id = $(this).val();
        let url = districts_url.replace(":regency_id", regency_id);

        if (regency_id) {
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $("#district")
                        .empty()
                        .append('<option value="">Pilih Kecamatan</option>');
                    $.each(data, function (key, value) {
                        $("#district").append(
                            `<option value="${value.id}">${value.name}</option>`
                        );
                    });
                },
            });
        } else {
            $("#district")
                .empty()
                .append('<option value="">Pilih Kecamatan</option>');
        }

        $("#village")
            .empty()
            .append('<option value="">Pilih Kelurahan</option>');
        $("#post_code").val("");
    });

    $("#district").on("change", function () {
        var district_id = $(this).val();
        let url = villages_url.replace(":district_id", district_id);

        if (district_id) {
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $("#village")
                        .empty()
                        .append('<option value="">Pilih Kelurahan</option>');
                    $.each(data, function (key, value) {
                        $("#village").append(
                            `<option value="${value.id}">${value.name}</option>`
                        );
                    });
                },
            });
        } else {
            $("#village")
                .empty()
                .append('<option value="">Pilih Kelurahan</option>');
        }

        $("#post_code").val("");
    });

    $("#village").on("change", function () {
        var village_id = $(this).val();
        let url = pos_code_url.replace(":village_id", village_id);

        if (village_id) {
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (data && data.post_code) {
                        $("#post_code").val(data.post_code);
                    } else {
                        $("#post_code").val("");
                    }
                },
            });
        } else {
            $("#post_code").val("");
        }
    });
});
