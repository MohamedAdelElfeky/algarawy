<!--begin::Modal - Invite Friends-->
<div class="modal fade" id="kt_modal_job" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">{!! getIcon('cross', 'fs-1') !!}
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
           
				<div class="fv-row mb-8">  
					<label class="fs-6 fw-semibold mb-2">Family</label>
                    <select class="form-select form-select-solid form-select-sm" data-control="select2"  data-hide-search="true">
                        <option  selected="selected">None</option>
                        <option value="1">Family 1</option>
                        <option value="2">Family 2</option>
                        <option value="3" >Family 3</option>
                    </select>
                </div>
				{{-- <div class="fv-row mb-8">
					<label class="fs-6 fw-semibold mb-2">Family</label>
					<input type="text" class="form-control form-control-solid" placeholder="Enter Family Name" name="Project Launch" />
				</div> --}}
                <div class="fv-row mb-8">
					<label class="fs-6 fw-semibold mb-2">Title</label>
					<input type="text" class="form-control form-control-solid" placeholder="Enter Title" name="Project Launch" />
				</div>
                <div class="fv-row mb-8">
					<label class="fs-6 fw-semibold mb-2">Description</label>
					<input type="text" class="form-control form-control-solid" placeholder="Enter Family Name" name="Project Launch" />
				</div>
                <div class="fv-row mb-8">
					<label class="fs-6 fw-semibold mb-2">Qualifications</label>
					<input type="text" class="form-control form-control-solid" placeholder="Enter Family Name" name="Project Launch" />
				</div>
                <div class="fv-row mb-8">
					<label class="fs-6 fw-semibold mb-2">location</label>
					<input type="text" class="form-control form-control-solid" placeholder="Enter Family Name" name="Project Launch" />
				</div>
                <div class="fv-row mb-8">
					<label class="fs-6 fw-semibold mb-2">Contact Details</label>
					<input type="text" class="form-control form-control-solid" placeholder="Enter Family Name" name="Project Launch" />
				</div>
                
    
                <!--begin::Textarea-->
                {{-- <textarea class="form-control form-control-solid mb-8" rows="3" placeholder="Type or paste emails here"></textarea> --}}
                <!--end::Textarea-->
                <!--begin::Users-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Invite Friend-->
