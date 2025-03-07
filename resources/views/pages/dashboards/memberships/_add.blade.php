<div class="modal fade" id="kt_modal_membership" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog mw-650px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    X
                </button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                <form id="membershipForm">
                    @csrf
                    <div class="fv-row mb-8">
                        <div class="row">
                            <div class="mb-3">
                                <label class="fs-6 fw-semibold mb-2">اسم العضوية</label>
                                <input type="text" name="name"
                                    class="form-control form-control-solid" placeholder="اسم العضوية" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="fs-6 fw-semibold mb-2"> النقاط المطلوبة </label>
                                <input type="number" name="points_required"
                                    class="form-control form-control-solid"
                                    required>
                                @error('points_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="fs-6 fw-semibold mb-2"> المزايا (اختياري)</label>
                                <textarea name="benefits" class="form-control form-control-solid"></textarea>
                            </div>

                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">حفظ</button>
                </form>
            </div>
        </div>
    </div>
</div>
