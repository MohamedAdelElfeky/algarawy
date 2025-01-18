<div class="modal fade" id="kt_modal_city" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog mw-650px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    X
                </button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                <form id="cityForm">
                    <div class="fv-row mb-8">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">اسم الحي</label>
                                <input type="text" class="form-control form-control-solid" placeholder="اسم الحي"
                                    name="name" required />
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">المنطقة</label>
                                <select class="form-select select2 form-select-solid" name="region_id">
                                    <option value="" selected disabled>اختر منطقة</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">حفظ</button>
                </form>
            </div>
        </div>
    </div>
</div>
