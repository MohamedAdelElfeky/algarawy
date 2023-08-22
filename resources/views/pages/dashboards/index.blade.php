<x-default-layout>
    <div class="row g-5 g-xl-8">
        <div class="col-xl-3">
            <a href="{{ route('userActive') }}" class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-duotone ki-people text-primary fs-3x ms-n1">
                        <i class="path1"></i>
                        <i class="path2"></i>
                        <i class="path3"></i>
                        <i class="path4"></i>
                        <i class="path5"></i>
                    </i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">{{ $userActive }}</div>
                    <div class="fw-semibold text-gray-400">المستخدمين</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3">
            <a href="{{ route('userNotActive') }}" class="card bg-dark hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-duotone ki-people text-gray-100 fs-3x ms-n1">
                        <i class="path1"></i>
                        <i class="path2"></i>
                        <i class="path3"></i>
                        <i class="path4"></i>
                        <i class="path5"></i>
                    </i>
                    <div class="text-gray-100 fw-bold fs-2 mb-2 mt-5">{{ $userNotActive }}</div>
                    <div class="fw-semibold text-gray-100">المستخدمين الجدد</div>
                </div>
                <!--end::Body-->
            </a>
            <!--end::Statistics Widget 5-->
        </div>
        <div class="col-xl-3">
            <!--begin::Statistics Widget 5-->
            <a href="{{ route('accountCharitySaving') }}" class="card bg-warning hoverable card-xl-stretch mb-xl-8">
                <!--begin::Body-->
                <div class="card-body">
                    <i class="ki-duotone ki-bank text-white fs-3x ms-n1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="text-white fw-bold fs-2 mb-2 mt-5">{{ $accountCharitySaving }}</div>
                    <div class="fw-semibold text-white"> حساب الصدقة والزكاة</div>
                </div>
                <!--end::Body-->
            </a>
            <!--end::Statistics Widget 5-->
        </div>
        <div class="col-xl-3">
            <!--begin::Statistics Widget 5-->
            <a href="{{ route('accountInvestment') }}" class="card bg-info hoverable card-xl-stretch mb-5 mb-xl-8">
                <!--begin::Body-->
                <div class="card-body">
                    <i class="ki-duotone ki-bank text-white fs-3x ms-n1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="text-white fw-bold fs-2 mb-2 mt-5">{{ $accountInvestment }}</div>
                    <div class="fw-semibold text-white">صندوق الادخار والاستثمار</div>
                </div>
                <!--end::Body-->
            </a>
            <!--end::Statistics Widget 5-->
        </div>
    </div>
    <div class="row g-5 g-xl-8">
        <div class="col-xl-3">
            <a href="{{ route('jobs.index') }}" class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-duotone ki-shield-search text-primary fs-3x ms-n1">
                        <i class="path1"></i>
                        <i class="path2"></i>
                        <i class="path3"></i>
                    </i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">{{ $job }}</div>
                    <div class="fw-semibold text-gray-400">الوظيفة</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3">
            <a href="{{ route('projects.index') }}" class="card bg-dark hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-duotone ki-graph-3 text-gray-100 fs-3x ms-n1">
                        <i class="path1"></i>
                        <i class="path2"></i>
                    </i>
                    <div class="text-gray-100 fw-bold fs-2 mb-2 mt-5">{{ $project }}</div>
                    <div class="fw-semibold text-gray-100"> المشاريع </div>
                </div>
                <!--end::Body-->
            </a>
            <!--end::Statistics Widget 5-->
        </div>
        <div class="col-xl-3">
            <!--begin::Statistics Widget 5-->
            <a href="{{ route('courses.index') }}" class="card bg-warning hoverable card-xl-stretch mb-xl-8">
                <!--begin::Body-->
                <div class="card-body">
                    <i class="ki-duotone ki-parcel-tracking text-white fs-3x ms-n1">
                        <i class="path1"></i>
                        <i class="path2"></i>
                        <i class="path3"></i>
                    </i>
                    <div class="text-white fw-bold fs-2 mb-2 mt-5">{{ $course }}</div>
                    <div class="fw-semibold text-white"> الدورات الاستشارية </div>
                </div>
                <!--end::Body-->
            </a>
            <!--end::Statistics Widget 5-->
        </div>
        <div class="col-xl-3">
            <!--begin::Statistics Widget 5-->
            <a href="{{ route('discounts.index') }}" class="card bg-info hoverable card-xl-stretch mb-5 mb-xl-8">
                <!--begin::Body-->
                <div class="card-body">
                    <i class="ki-duotone ki-discount text-white fs-3x ms-n1">
                        <i class="path1"></i>
                        <i class="path2"></i>
                    </i>
                    <div class="text-white fw-bold fs-2 mb-2 mt-5">{{ $discount }}</div>
                    <div class="fw-semibold text-white"> الخصومات والعروض </div>
                </div>
                <!--end::Body-->
            </a>
            <!--end::Statistics Widget 5-->
        </div>
    </div>
</x-default-layout>
