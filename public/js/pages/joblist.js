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
                                } else if (response.code == 201) {
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

    // delete button -> user Delete
    $(".delete-post-btn").on("click", function () {
        let id = $(this).parents(".job-item").attr("post_id");
        let _url = "/my_job/" + id;

        $.ajax({
            type: "DELETE",
            url: _url,
            success: function (data) {
                // $("tr[post_id = " + id + "]").remove();
                location.reload();
            },
            error: function (data) {
                console.log("Error:", data);
            },
        });
    });

    var response_modal = document.querySelector("#kt_modal_response");
    var response_modal_object = new bootstrap.Modal(response_modal);
    var response_form = document.querySelector("#kt_modal_response_form");
    var response_send_btn = document.getElementById("kt_modal_response_submit");
    var response_cancel_btn = document.getElementById(
        "kt_modal_response_cancel"
    );
    var validation = FormValidation.formValidation(response_form, {
        fields: {
            m_response: {
                validators: {
                    notEmpty: {
                        message: "Message is required",
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

    // modal submit button
    response_send_btn.addEventListener("click", function (e) {
        e.preventDefault(),
            validation &&
                validation.validate().then(function (e) {
                    if ("Valid" == e) {
                        response_send_btn.setAttribute(
                            "data-kt-indicator",
                            "on"
                        );
                        response_send_btn.disabled = !0;

                        let _url = "/job_list";

                        let data = {
                            job_id: $("#m_target_post_id").val(),
                            owner_id: $("#m_target_post_owner_id").val(),
                            message: $("#m_response").val(),
                        };

                        $.ajax({
                            type: "POST",
                            url: _url,
                            data: data,
                            success: function (response) {
                                if (response.code == 200) {
                                    location.reload();
                                } else if (response.code == 201) {
                                    response_send_btn.removeAttribute(
                                        "data-kt-indicator"
                                    );
                                    response_send_btn.disabled = !1;
                                    response_modal_object.hide();

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
                                response_send_btn.removeAttribute(
                                    "data-kt-indicator"
                                );
                                response_send_btn.disabled = !1;
                                response_modal_object.hide();
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
    response_cancel_btn.addEventListener("click", function (t) {
        t.preventDefault();
        response_form.reset(), response_modal_object.hide();
    });

    // Response Button click
    $(".response-post-btn").on("click", function () {
        let target_id = $(this).parents(".job-item").attr("post_id");
        $("#m_target_post_id").val(target_id);
        $("#m_target_post_owner_id").val(
            $(this).parents(".job-item").attr("owner_id")
        );

        $("#m_target_job_title").html(
            $(this).parents(".job-item").attr("job_title")
        );
        $("#m_target_job_description").html(
            $(this).parents(".job-item").attr("job_description")
        );

        response_modal_object.show();
    });

    // Like btn click
    $(".like-post-btn").on("click", function () {
        let target_id = $(this).parents(".job-item").attr("post_id");

        let _url = "/job_list/like";
        let data = {
            like_target_id: target_id,
        };

        $.ajax({
            type: "POST",
            url: _url,
            data: data,
            success: function (response) {
                location.reload();
            },
            error: function (data) {},
        });
    });
});
