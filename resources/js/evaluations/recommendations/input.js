$(document).ready(function () {
    function updateCardContent(semesterData) {
        $(".card-container").empty();

        if (
            !semesterData ||
            !semesterData.subjects ||
            semesterData.subjects.length === 0
        ) {
            $(".card-container").append(
                "<p>Tidak ada mata kuliah yang tersedia untuk direkomendasikan saat ini.</p>"
            );
            return;
        }

        let cardHtml = `
      <div class="col-lg-12">
          <div class="card push">
              <div class="card-header border-bottom-0">
                  <h3 class="block-title">
                      Semester ${semesterData.semester}
                  </h3>
              </div>
              <div class="card-body">
                  <div class="">
                      <div class="form-check">
                          <input class="form-check-input select-all" type="checkbox" id="select-all-${semesterData.semester}">
                          <label class="form-check-label" for="select-all-${semesterData.semester}">Pilih Semua</label>
                      </div>
      `;

        semesterData.subjects.forEach(function (subject) {
            cardHtml += `
          <div class="form-check">
              <input class="form-check-input subject-checkbox" type="checkbox" id="checkbox-${subject.id}" name="subjects[]" value="${subject.id}">
              <label class="form-check-label" for="checkbox-${subject.id}">${subject.subject_name}</label>
          </div>
          `;
        });

        cardHtml += `
                  </div>
              </div>
          </div>
      </div>
      `;

        $(".card-container").append(cardHtml);

        // Implementasi "Pilih Semua" untuk semester
        $(".select-all").on("change", function () {
            let checked = $(this).prop("checked");
            $(this)
                .closest(".card")
                .find(".subject-checkbox")
                .prop("checked", checked);
        });
    }

    function updateStudentDetails(studentId) {
        let url = studentDetailURL.replace(":student_id", studentId);
        $.ajax({
            url: url,
            method: "GET",
            success: function (data) {
                $("li:contains('NIM') span").text(data.nim);
                $("li:contains('Program Studi') span").text(data.major_name);
            },
            error: function () {
                $("li:contains('NIM') span").text("--");
                $("li:contains('Program Studi') span").text("--");
            },
        });
    }

    function toggleCardVisibility(studentId) {
        if (studentId) {
            $(".card-container").show();
            $(".loading-indicator").show();
            updateStudentDetails(studentId);

            let url = studentURL.replace(":student_id", studentId);

            $.ajax({
                url: url,
                method: "GET",
                success: function (data) {
                    updateCardContent(data);
                },
                error: function (xhr) {
                    if (xhr.status === 404) {
                        $(".card-container").html(
                            "<p>Tidak ada mata kuliah yang tersedia untuk direkomendasikan saat ini.</p>"
                        );
                    } else {
                        $(".card-container").html(
                            "<p>Terjadi kesalahan. Silakan coba lagi.</p>"
                        );
                    }
                },
                complete: function () {
                    $(".loading-indicator").hide();
                },
            });
        } else {
            $(".card-container").hide();
            $("li:contains('NIM') span").text("--");
            $("li:contains('Program Studi') span").text("--");
        }
    }

    $("#student_id").on("change", function () {
        let studentId = $(this).val();
        toggleCardVisibility(studentId);
    });

    toggleCardVisibility($("#student_id").val());

    $("form").on("submit", function (e) {
        if ($("input[name='subjects[]']:checked").length === 0) {
            e.preventDefault();
            alert("Pilih setidaknya satu mata kuliah");
        }
    });
});
