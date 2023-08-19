<div class="modal fade" id="kt_modal_charity_saving" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog mw-650px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    X
                </button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                <form id="bankAccountForm">
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">رقم حساب </label>
                        <input type="text" class="form-control form-control-solid" placeholder="رقم حساب"
                            name="account_number" required />
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">IBAN</label>
                        <input type="text" class="form-control form-control-solid" placeholder="IBAN" name="iban"
                            required />
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">اسم البنك </label>
                        <input type="text" class="form-control form-control-solid" placeholder="اسم البنك"
                            name="bank_name" required />
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">Swift Number </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Swift Number"
                            name="swift_number" required />
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">نوع الحساب</label>
                        <select class="form-control form-control-solid" name="type" required>
                            <option>اختيار نوع الحساب</option>
                            <option value="charity">الاستثمار</option>
                            <option value="saving">الادخار</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

