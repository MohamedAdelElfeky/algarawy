<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">المستخدمين</span>
                </h3>
                <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                    <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_admin">
                        <i class="ki-duotone ki-plus fs-2"></i>أضافة</a>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th>الاسم الأول</th>
                            <th>الاسم الأخير</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>الموقع</th>
                            <th>الصورة الرمزية</th>
                            <th>صورة البطاقة الأمامية</th>
                            <th>صورة البطاقة الخلفية</th>
                            <th>تاريخ الميلاد</th>
                            <th>الهوية الوطنية</th>
                            <th> تغير كلمه المرور </th>

                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        @if ($user->region && $user->neighborhood)
                                            {{ $user->region->name . ' ' . $user->region->city . ' ' . $user->neighborhood->name }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ $user->avatar !== null && $user->avatar !== '' ? asset($user->avatar) : asset('default.png') }}"
                                                    alt="" />
                                            </div>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ $user->national_card_image_front !== null && $user->national_card_image_front !== '' ? asset($user->national_card_image_front) : asset('default.png') }}"
                                                    alt="" />


                                            </div>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ $user->national_card_image_back !== null && $user->national_card_image_back !== '' ? asset($user->national_card_image_back) : asset('default.png') }}"
                                                    alt="" />


                                            </div>

                                        </div>
                                    </td>
                                    <td>{{ $user->birth_date }}</td>
                                    <td>{{ $user->national_id }}</td>
                                    <td>

                                        <button class="btn btn-sm btn-primary change-password-btn"
                                            data-user-id="{{ $user->id }}" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_admin">
                                            تغيير كلمة المرور
                                        </button>
                                        @include('pages/dashboards/admin/_edit_password')

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('pages/dashboards/admin/_add')
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

            // $(document).ready(function() {
            //     $('.change-password-btn').click(function() {
            //         const userId = $(this).data('user-id');
            //         // Set user ID in a hidden input field within the form
            //         $('#user-id').val(userId);
            //     });

            //     $('#adminForm').submit(function(e) {
            //         e.preventDefault();
            //         var formData = $(this).serialize();
            //         $.ajax({
            //             type: 'POST',
            //             url: 'admin/change-password',
            //             data: formData,
            //             headers: {
            //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //             },
            //             success: function(response) {
            //                 Swal.fire({
            //                     icon: 'success',
            //                     title: 'Success!',
            //                     text: 'Password has been changed successfully.',
            //                     showConfirmButton: false,
            //                     timer: 1500
            //                 }).then(function() {
            //                     // Close the modal
            //                     $('#kt_modal_admin').modal('hide');
            //                     location.reload();
            //                 });
            //             },
            //             error: function(error) {
            //                 Swal.fire({
            //                     icon: 'error',
            //                     title: 'Error!',
            //                     text: 'Failed to change password.',
            //                     showConfirmButton: false,
            //                     timer: 1500
            //                 });
            //             }
            //         });
            //     });
            // });

            $(document).ready(function() {
                $('#change-password-form').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        type: 'POST',
                        url: 'changePasswordByAdmin',
                        data: $(this).serialize(),
                        success: function(response) {
                            $('#kt_modal_admin').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'تم تغيير كلمة المرور بنجاح',
                                showConfirmButton: false,
                                timer: 1500 // Close the alert after 1.5 seconds
                            });
                        },
                        error: function(xhr) {
                            $('#kt_modal_admin').modal('hide');
                            if (xhr.status === 404) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: 'المستخدم غير موجود'
                                });
                            } else if (xhr.status === 400) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: 'كلمة المرور القديمة غير صحيحة'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: 'حدث خطأ ما'
                                });
                            }
                        }
                    });
                });
            });
        </script>
    @endsection
</x-default-layout>
