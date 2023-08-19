<!--begin::sidebar menu-->
<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true"
        data-kt-scroll-activate="true" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
        <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true"
            data-kt-menu-expand="false">
            <div data-kt-menu-trigger="click" class="menu-item here show menu-accordion">

                <div class="menu-item">
                    <a class="menu-link" href="{{ url('admin') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title"> مديرين الموقع </span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link" href="{{ url('userActive') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">المستخدمين </span>
                    </a>
                </div>

                <div class="menu-item">

                    <a class="menu-link" href="{{ url('userNotActive') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">المستخدمين الجدد</span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ url('accountCharitySaving') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">حساب الصدقة والزكاة</span>
                    </a>
                </div>


                <div class="menu-item">

                    <a class="menu-link" href="{{ url('accountInvestment') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">صندوق الادخار والاستثمار</span>
                    </a>
                </div>

                <div class="menu-item">

                    <a class="menu-link" href="{{ url('support') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title"> الدعم الفني </span>
                    </a>
                </div>
            </div>
        </div>
        <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>
<!--end::sidebar menu-->
