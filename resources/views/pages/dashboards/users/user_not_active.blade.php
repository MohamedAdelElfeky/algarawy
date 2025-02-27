<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">المستخدمين</span>
                </h3>
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
                            <th> تفعيل المستخدم</th>
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
                                        {{ optional($user->details)->getFullLocation() }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ optional($user->details)->getImageByType('avatar') }}"
                                                    alt="User Avatar" class="img-thumbnail img-clickable"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    data-img-src="{{ optional($user->details)->getImageByType('avatar') }}" />
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ optional($user->details)->getImageByType('national_card_image_front') }}"
                                                    alt="National Card Front" class="img-thumbnail img-clickable"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    data-img-src="{{ optional($user->details)->getImageByType('national_card_image_front') }}" />
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ optional($user->details)->getImageByType('national_card_image_back') }}"
                                                    alt="National Card Back" class="img-thumbnail img-clickable"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    data-img-src="{{ optional($user->details)->getImageByType('national_card_image_back') }}" />
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ optional($user->details)->birthdate }}</td>
                                    <td>{{ $user->national_id }}</td>
                                    <td>
                                        <button
                                            class="toggle-user-btn btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                            data-user-id="{{ $user->id }}"
                                            data-user-active="{{ $user->registration_confirmed }}">
                                            <i class="ki-duotone ki-toggle-on-circle fs-2">
                                                <i class="path1"></i>
                                                <i class="path2"></i>
                                            </i>
                                        </button>
                                    </td>
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
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Preview Image">
                </div>
            </div>
        </div>
    </div>
    @section('script')
        <script>
            $(document).ready(function() {
                $('.toggle-user-btn').click(function(event) {
                    event.preventDefault();
                    const button = $(this);
                    const userId = button.attr('data-user-id');
                    const isActive = button.attr('data-user-active') === '1';
                    const url = `toggle-user/${userId}`;
                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            if (data.success) {
                                button.attr('data-user-active', isActive ? '1' : '0');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم تفعيل المستخدم',
                                    text: 'تم تفعيل المستخدم بنجاح.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(error) {
                            console.error('An error occurred:', error);
                        }
                    });
                });
            });

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
            document.addEventListener("DOMContentLoaded", function() {
                let modalImage = document.getElementById("modalImage");
                document.querySelectorAll(".img-clickable").forEach(img => {
                    img.addEventListener("click", function() {
                        modalImage.src = this.getAttribute("data-img-src");
                    });
                });
            });
        </script>
    @endsection
</x-default-layout>
