"use strict";
var KTSignupGeneral = (function () {
    return {
        init: function () {
            // sign in
            let signin_form = document.querySelector("#kt_sign_in_form");
            let signin_btn = document.querySelector("#kt_sign_in_submit");
            let form_validation = FormValidation.formValidation(signin_form, {
                fields: {
                    email: {
                        validators: {
                            notEmpty: {
                                message: "Email is required",
                            },
                            emailAddress: {
                                message: "The value is not a valid email address",
                            },
                        },
                    },
                    password: {
                        validators: {
                            notEmpty: {
                                message: "Password is required",
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                    }),
                },
            });
            signin_btn.addEventListener("click", function (n) {
                n.preventDefault();
                form_validation.validate().then(function (i) {
                    if ("Valid" == i) {
                        signin_btn.setAttribute("data-kt-indicator", "on");
                        signin_btn.disabled = !0;

                        let _url = "/login";
                        let _token = $('meta[name="csrf-token"]').attr(
                            "content"
                        );

                        let data = {
                            email: $("#email").val(),
                            password: $("#password").val(),
                            _token: _token,
                        };

                        $.ajax({
                            url: _url,
                            type: "POST",
                            data: data,
                            success: function (response) {
                                signin_btn.setAttribute(
                                    "data-kt-indicator",
                                    "off"
                                );
                                signin_btn.disabled = !1;
                                if (response.code == 200) {
                                    location.href = "/";
                                } else {
                                    Swal.fire({
                                        text: "You have entered invalid login details",
                                        icon: "warning",
                                        buttonsStyling: !1,
                                        confirmButtonText: "Ok!",
                                        customClass: {
                                            confirmButton: "btn btn-success",
                                        },
                                    });
                                }
                            },
                            error: function (response) {
                                Swal.fire({
                                    text: "Login failed. Please try again later",
                                    icon: "error",
                                    buttonsStyling: !1,
                                    confirmButtonText: "Ok!",
                                    customClass: {
                                        confirmButton: "btn btn-success",
                                    },
                                });
                                signin_btn.setAttribute(
                                    "data-kt-indicator",
                                    "off"
                                );
                                signin_btn.disabled = !1;
                            },
                        });
                    } else {
                        Swal.fire({
                            text: "Login failed. Please try again later",
                            icon: "error",
                            buttonsStyling: !1,
                            confirmButtonText: "Ok!",
                            customClass: {
                                confirmButton: "btn btn-success",
                            },
                        });
                    }
                });
            });
        },
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTSignupGeneral.init();
});
