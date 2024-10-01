$(document).ready(function () {
    $("#subjects")
        .select2({
            width: "100%",
            placeholder: "Pilih Matakuliah",
            ajax: {
                url: urlMajorSubjects,
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                        page: params.page,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id,
                            };
                        }),
                    };
                },
                cache: true,
            },
        })
        .on("select2:open", function () {
            $(this).show();
        })
        .on("select2:ready", function () {
            $(this).css("width", "100%");
        });

    // Pre-populate selected options
    if (oldSubjects.length > 0) {
        $.ajax({
            url: urlMajorSubjects,
            type: "GET",
            dataType: "json",
        }).then(function (data) {
            // Create the options and append to Select2
            var options = data
                .filter(function (subject) {
                    return oldSubjects.includes(subject.id.toString());
                })
                .map(function (subject) {
                    return new Option(subject.name, subject.id, true, true);
                });

            $("#subjects").append(options).trigger("change");
        });
    }
});
