<x-auth-layout>

    <!--begin::Form-->
    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="{{ route('dashboard') }}" action="login">
        @csrf
        <!--begin::Heading-->
        <div class="text-center mb-11">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">
                تسجيل الدخول
            </h1>
            <!--end::Title-->

            <!--begin::Subtitle-->
            {{-- <div class="text-gray-500 fw-semibold fs-6">
                Your Social Campaigns
            </div> --}}
            <!--end::Subtitle--->
        </div>
        <!--begin::Heading-->

        <!--begin::Login options-->
        {{-- <div class="row g-3 mb-9">
            <!--begin::Col-->
            <div class="col-md-6">
                <!--begin::Google link--->
                <a href="{{ url('/auth/redirect/google') }}?redirect_uri={{ url()->current() }}" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                    <img alt="Logo" src="{{ image('svg/brand-logos/google-icon.svg') }}" class="h-15px me-3"/>
                    Sign in with Google
                </a>
                <!--end::Google link--->
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-md-6">
                <!--begin::Google link--->
                <a href="{{ url('/auth/redirect/apple') }}?redirect_uri={{ url()->current() }}" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                    <img alt="Logo" src="{{ image('svg/brand-logos/apple-black.svg') }}" class="theme-light-show h-15px me-3"/>
                    <img alt="Logo" src="{{ image('svg/brand-logos/apple-black-dark.svg') }}" class="theme-dark-show h-15px me-3"/>
                    Sign in with Apple
                </a>
                <!--end::Google link--->
            </div>
            <!--end::Col-->
        </div> --}}
        <!--end::Login options-->

        <!--begin::Separator-->
        {{-- <div class="separator separator-content my-14">
            <span class="w-125px text-gray-500 fw-semibold fs-7">Or with email</span>
        </div> --}}
        <!--end::Separator-->

        <!--begin::Input group--->
        <div class="fv-row mb-8">
            <!--begin::Email-->
            <input type="text" placeholder="رقم الهوية" name="national_id" autocomplete="off" class="form-control bg-transparent"/>
            <!--end::Email-->
        </div>

        <!--end::Input group--->
        <div class="fv-row mb-3">
            <!--begin::Password-->
            <input type="password" placeholder="كلمة المرور" name="password" autocomplete="off" class="form-control bg-transparent"/>
            <!--end::Password-->
        </div>
        <!--end::Input group--->

        <!--begin::Wrapper-->
        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
            <div></div>

            <!--begin::Link-->
            <a href="{{ route('password.request') }}" class="link-primary">
                هل نسيت كلمة السر ؟
            </a>
            <!--end::Link-->
        </div>
        <!--end::Wrapper-->

        <!--begin::Submit button-->
        <div class="d-grid mb-10">
            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                @include('partials/general/_button-indicator', ['label' => 'تسجيل الدخول'])
            </button>
        </div>
        <!--end::Submit button-->

        <!--begin::Sign up-->
        <div class="text-gray-500 text-center fw-semibold fs-6">
            لست عضوا حتى الآن ؟ 

            {{-- <a href="/register" class="link-primary">
                اشتراك
            </a> --}}
        </div>
        <!--end::Sign up-->
    </form>
    <!--end::Form-->

</x-auth-layout>
