@extends('layouts.public_app')
@section('content')
<div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed">
    <!--begin::Content-->
    <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
       
        <!--begin::Wrapper-->
        <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
            <!--begin::Form-->
            <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form">
                <!--begin::Heading-->
                <div class="text-center mb-10">
                    <!--begin::Title-->
                    <h1 class="text-dark mb-3">Sign In</h1>
                    <!--end::Title-->
                </div>
                <!--begin::Heading-->
                <!--begin::Input group-->
                <div class="fv-row mb-10">
                    <!--begin::Label-->
                    <label class="form-label fs-6 fw-bolder text-dark">Email</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-lg form-control-solid" type="text" name="email" id="email" autocomplete="off" />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="fv-row mb-10">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-stack mb-2">
                        <!--begin::Label-->
                        <label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
                        <!--end::Label-->
                    </div>
                    <!--end::Wrapper-->
                    <!--begin::Input-->
                    <input class="form-control form-control-lg form-control-solid" type="password" name="password" id="password" autocomplete="off" />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Actions-->
                <div class="text-center">
                    <!--begin::Submit button-->
                    <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-success w-100 mb-5">
                        <span class="indicator-label">Continue</span>
                        <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <!--end::Submit button-->
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Content-->
</div>
@endsection


@push('scripts')
    <script src="{{ asset('js/pages/login.js')}}"></script>
@endpush