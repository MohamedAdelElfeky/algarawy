<!--begin::sidebar menu-->
<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true"
        data-kt-scroll-activate="true" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
        <!--begin::Menu-->
        <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true"
            data-kt-menu-expand="false">
            <!--begin:Menu item-->
            <div data-kt-menu-trigger="click" class="menu-item here show menu-accordion">
                <!--begin:Menu link-->
                <div class="menu-item">
                    {{-- {{ csrf_field() }}        --}}
                    <a class="menu-link" href="{{ url('family') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">Family</span>
                    </a>
                </div>

                <div class="menu-item">

                    <a class="menu-link" href="{{ url('job') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">Job</span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ url('project') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">Project</span>
                    </a>
                </div>

                <div class="menu-item">

                    <a class="menu-link" href="{{ url('discount') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">Discount</span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ url('course') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">Course</span>
                    </a>
                </div>
                <div class="menu-item">

                    <a class="menu-link" href="{{ url('type_of_relationships') }}">
                        <span class="menu-bullet">
                            <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        </span>
                        <span class="menu-title">Type Of Relationships</span>
                    </a>
                </div>
            </div>
        </div>
        <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>
<!--end::sidebar menu-->
