$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $("#kt_modal_add_form").on("hidden.bs.modal", function (event) {
        $(this).find("form").trigger("reset");
        $(".action-type").html("Post");
        $("#kt_modal_new_target_submit .indicator-label").html("Post");
        $("#m_post_id").val("");
    });

    var datatable = $("#kt_datatable").DataTable({
        info: !1,
        order: [],
        // pageLength: 5,
        // lengthChange: !1,
    });

    document
        .querySelector('[data-kt-permissions-table-filter="search"]')
        .addEventListener("keyup", function (e) {
            datatable.search(e.target.value).draw();
        });

    var t, e, n, a, o, i;
    i = document.querySelector("#kt_modal_add_form");
    o = new bootstrap.Modal(i);
    a = document.querySelector("#kt_modal_new_target_form");
    t = document.getElementById("kt_modal_new_target_submit");
    e = document.getElementById("kt_modal_new_target_cancel");
    n = FormValidation.formValidation(a, {
        fields: {
            m_job_title: {
                validators: {
                    notEmpty: {
                        message: "Job title is required",
                    },
                },
            },
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap5({
                rowSelector: ".fv-row",
                eleInvalidClass: "",
                eleValidClass: "",
            }),
        },
    });

    // edit mode
    $(".edit-post-btn").on("click", function () {
        let id = $(this).parents(".job-item").attr("post_id");

        $("#m_post_id").val(id);

        $("#m_job_title").val($(this).parents(".job-item").attr("job_title"));
        $("#m_job_description").val(
            $(this).parents(".job-item").attr("job_description")
        );

        $(".action-type").html("Edit");
        $("#kt_modal_new_target_submit .indicator-label").html("Update");
        o.show();
    });

    // modal submit button
    t.addEventListener("click", function (e) {
        e.preventDefault(),
            n &&
                n.validate().then(function (e) {
                    if ("Valid" == e) {
                        t.setAttribute("data-kt-indicator", "on");
                        t.disabled = !0;

                        let _url = "/my_job";

                        let data = {
                            title: $("#m_job_title").val(),
                            description: $("#m_job_description").val(),
                        };

                        if ($(".action-type").html() == "Edit") {
                            data["id"] = $("#m_post_id").val();
                        }

                        $.ajax({
                            type: "POST",
                            url: _url,
                            data: data,
                            success: function (response) {
                                if (response.code == 200) {
                                    location.reload();
                                } else {
                                    t.removeAttribute("data-kt-indicator");
                                    t.disabled = !1;
                                    o.hide();

                                    Swal.fire({
                                        text: response.message,
                                        icon: "error",
                                        buttonsStyling: !1,
                                        confirmButtonText: "OK",
                                        customClass: {
                                            confirmButton: "btn btn-success",
                                        },
                                    });
                                }
                            },
                            error: function (data) {
                                console.log("Error:", data);
                                t.removeAttribute("data-kt-indicator");
                                t.disabled = !1;
                                o.hide();
                            },
                        });
                    } else {
                        Swal.fire({
                            text: "Sorry! Please fill in the input fields",
                            icon: "error",
                            buttonsStyling: !1,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-success",
                            },
                        });
                    }
                });
    });

    // modal cancel button
    e.addEventListener("click", function (t) {
        t.preventDefault();
        a.reset(), o.hide();
    });

    // active button

    // delete button -> user Delete
    $(".delete-post-btn").on("click", function () {
        let id = $(this).parents(".job-item").attr("post_id");
        let _url = "/my_job/" + id;

        $.ajax({
            type: "DELETE",
            url: _url,
            success: function (data) {
                $("tr[post_id = " + id + "]").remove();
            },
            error: function (data) {
                console.log("Error:", data);
            },
        });
    });
});
