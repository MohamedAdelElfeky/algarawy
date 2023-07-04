<x-default-layout>
    <x-default-layout>
        <!--begin::Tables Widget 9-->
        <div class="card mb-5 mb-xl-8">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Jobs</span>
                    {{-- <span class="text-muted mt-1 fw-semibold fs-7">Over 500 members</span> --}}
                </h3>
                <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                    title="Click to add a Family">
                    <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_job">
                        <i class="ki-duotone ki-plus fs-2"></i>New</a>
                </div>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body py-3">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="w-25px">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1" data-kt-check="true"
                                            data-kt-check-target=".widget-9-check" />
                                    </div>
                                </th>
                                <th class="min-w-200px">Id</th>
                                <th class="min-w-150px">Family</th>
                                <th class="min-w-150px">Title</th>
                                <th class="min-w-150px">Description</th>
                                <th class="min-w-150px"> Qualifications</th>
                                <th class="min-w-150px"> location</th>
                                <th class="min-w-150px"> Contact Details</th>
                                <th class="min-w-100px text-end">Actions</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                     
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
            <!--begin::Body-->
        </div>
        <!--end::Tables Widget 9-->
        @include('admin/job/_add_model')
    
    </x-default-layout>
    
</x-default-layout>
