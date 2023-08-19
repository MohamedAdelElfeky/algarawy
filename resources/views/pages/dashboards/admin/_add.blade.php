<div class="modal fade" id="kt_modal_admin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog mw-650px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    X
                </button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                <form id="adminForm">
                    <div class="fv-row mb-8">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">الاسم الأول</label>
                                <input type="text" class="form-control form-control-solid" placeholder="الاسم الأول"
                                    name="first_name" required />
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">الاسم الأخير</label>
                                <input type="text" class="form-control form-control-solid" placeholder="الاسم الأخير"
                                    name="last_name" required />
                            </div>
                        </div>
                    </div>
                    <div class="fv-row mb-8">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">البريد الإلكتروني</label>
                                <input type="email" class="form-control form-control-solid"
                                    placeholder="البريد الإلكتروني" name="email" required />
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">الهاتف</label>
                                <input type="tel" class="form-control form-control-solid" placeholder="الهاتف"
                                    name="phone" required />
                            </div>
                        </div>
                    </div>
                    <div class="fv-row mb-8">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">كلمة المرور</label>
                                <input type="password" class="form-control form-control-solid" placeholder="كلمة المرور"
                                    name="password" required />
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">كلمة المرور</label>
                                <input type="password" class="form-control form-control-solid" placeholder="كلمة المرور"
                                    name="password_confirmation" required />
                            </div>

                        </div>
                    </div>
                    <div class="fv-row mb-8">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">تاريخ الميلاد</label>
                                <input type="date" class="form-control form-control-solid" name="birth_date"
                                    required />
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">الرقم الوطني</label>
                                <input type="text" class="form-control form-control-solid" placeholder="الرقم الوطني"
                                    name="national_id" required />
                            </div>
                        </div>
                    </div>
                    <div class="fv-row mb-8">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">صورة البطاقة الوطنية (الجهة الأمامية)</label>
                                <input type="file" class="form-control form-control-solid"
                                    name="national_card_image_front" />
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">صورة البطاقة الوطنية (الجهة الخلفية)</label>
                                <input type="file" class="form-control form-control-solid"
                                    name="national_card_image_back" />
                            </div>
                        </div>
                    </div>
                    <div class="fv-row mb-8">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">الصورة الشخصية</label>
                                <input type="file" class="form-control form-control-solid" name="avatar" />
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('script')
    <script>
        $(document).ready(function() {
            $('#adminForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'admin/add-user',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'نجاح!',
                            text: 'تمت إضافة المستخدم بنجاح.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            location.reload();
                        });
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: 'فشل إضافة المستخدم.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });
        });
    </script>
@endsection
