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
                            <i class="fas fa-user-shield me-2"></i>
                        </span>
                        <span class="menu-title"> مديرين الموقع </span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link" href="{{ url('userActive') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-user me-2"></i>
                        </span>
                        <span class="menu-title">المستخدمين </span>
                    </a>
                </div>

                <div class="menu-item">

                    <a class="menu-link" href="{{ url('userNotActive') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-user-friends me-2"></i>
                        </span>
                        <span class="menu-title">المستخدمين الجدد</span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ url('accountCharitySaving') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-hand-holding-heart me-2"></i>
                        </span>
                        <span class="menu-title">حساب الصدقة والزكاة</span>
                    </a>
                </div>


                <div class="menu-item">

                    <a class="menu-link" href="{{ url('accountInvestment') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-bank me-2"></i>
                        </span>
                        <span class="menu-title">صندوق الادخار والاستثمار</span>
                    </a>
                </div>

                <div class="menu-item">

                    <a class="menu-link" href="{{ url('support') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-headset me-2"></i>
                        </span>
                        <span class="menu-title"> الدعم الفني </span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ route('jobs.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-briefcase me-2"></i>
                        </span>
                        <span class="menu-title"> الوظيفة </span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ route('projects.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-tasks me-2"></i>
                        </span>
                        <span class="menu-title"> المشاريع </span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ route('web-courses.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-bookmark me-2"></i>
                        </span>
                        <span class="menu-title"> الدورات الاستشارية </span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ route('meetings.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-users me-2"></i>
                        </span>
                        <span class="menu-title"> الاجتماعات </span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ route('services.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-cogs me-2"></i>
                        </span>
                        <span class="menu-title"> الخدمات </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('discounts.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-percent me-2"></i>
                        </span>
                        <span class="menu-title"> الخصومات والعروض </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('regions.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-map-marked-alt me-2"></i>
                        </span>
                        <span class="menu-title"> المنطقه </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('cities.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-city me-2"></i>
                        </span>
                        <span class="menu-title"> المدينة </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('neighborhoods.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-city me-2"></i>
                        </span>
                        <span class="menu-title"> الحى </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('user.import.form') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-user-plus me-2"></i>
                        </span>
                        <span class="menu-title"> استيراد الأعضاء </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('memberships.index') }}">
                        <span class="menu-bullet">
                            <i class="fas fa-id-card me-2"></i>
                        </span>
                        <span class="menu-title"> العضويات </span>
                    </a>
                </div>
            </div>
        </div>
        <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>
<!--end::sidebar menu-->
