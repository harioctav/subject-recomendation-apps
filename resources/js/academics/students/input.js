$(() => {
    const today = new Date();

    // Initialize flatpickr for birth_date
    flatpickr("#birth_date", {
        dateFormat: "Y-m-d",
        maxDate: today,
    });

    // Function to handle AJAX requests and populate options
    function populateSelect(
        url,
        $select,
        defaultOption,
        oldVal = null,
        callback = null
    ) {
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (data) {
                $select
                    .empty()
                    .append(`<option value="">${defaultOption}</option>`);
                $.each(data, function (key, value) {
                    const optionText = value.type
                        ? `${value.type} ${value.name}`
                        : value.name; // Include type if available
                    $select.append(
                        `<option value="${value.id}" ${
                            value.id == oldVal ? "selected" : ""
                        }>${optionText}</option>`
                    );
                });
                if (callback) callback();
            },
        });
    }

    // Load regency, district, and village based on old values
    function loadOldValues() {
        const oldProvince = $("#province").data("old");
        const oldRegency = $("#regency").data("old");
        const oldDistrict = $("#district").data("old");
        const oldVillage = $("#village").data("old");
        const oldPostCode = $("#post_code").data("old");

        if (oldProvince) {
            $("#province").val(oldProvince).trigger("change");
            loadRegency(
                oldProvince,
                oldRegency,
                oldDistrict,
                oldVillage,
                oldPostCode
            );
        }
    }

    // Load regency, district, and village based on the provided old values
    function loadRegency(
        province_id,
        oldRegency,
        oldDistrict,
        oldVillage,
        oldPostCode
    ) {
        const url = regencies_url.replace(":province_id", province_id);
        populateSelect(
            url,
            $("#regency"),
            "Pilih Kabupaten",
            oldRegency,
            function () {
                if (oldRegency)
                    loadDistrict(
                        oldRegency,
                        oldDistrict,
                        oldVillage,
                        oldPostCode
                    );
            }
        );
    }

    function loadDistrict(regency_id, oldDistrict, oldVillage, oldPostCode) {
        const url = districts_url.replace(":regency_id", regency_id);
        populateSelect(
            url,
            $("#district"),
            "Pilih Kecamatan",
            oldDistrict,
            function () {
                if (oldDistrict)
                    loadVillage(oldDistrict, oldVillage, oldPostCode);
            }
        );
    }

    function loadVillage(district_id, oldVillage, oldPostCode) {
        const url = villages_url.replace(":district_id", district_id);
        populateSelect(
            url,
            $("#village"),
            "Pilih Kelurahan",
            oldVillage,
            function () {
                if (oldPostCode) $("#post_code").val(oldPostCode);
            }
        );
    }

    // Event handler for province change
    $("#province").on("change", function () {
        const province_id = $(this).val();
        resetSelects(["#regency", "#district", "#village"], "#post_code");
        if (province_id) loadRegency(province_id);
    });

    // Event handler for regency change
    $("#regency").on("change", function () {
        const regency_id = $(this).val();
        resetSelects(["#district", "#village"], "#post_code");
        if (regency_id) loadDistrict(regency_id);
    });

    // Event handler for district change
    $("#district").on("change", function () {
        const district_id = $(this).val();
        resetSelects(["#village"], "#post_code");
        if (district_id) loadVillage(district_id);
    });

    // Event handler for village change
    $("#village").on("change", function () {
        const village_id = $(this).val();
        $("#post_code").val(""); // Reset post_code
        if (village_id) {
            const url = pos_code_url.replace(":village_id", village_id);
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $("#post_code").val(data.post_code || "");
                },
            });
        }
    });

    // Reset selected elements
    function resetSelects(selectors, postCodeSelector) {
        selectors.forEach((selector) => {
            $(selector)
                .empty()
                .append(
                    `<option value="">Pilih ${$(selector).attr("id")}</option>`
                );
        });
        $(postCodeSelector).val("");
    }

    // Load old values if they exist
    loadOldValues();
});
