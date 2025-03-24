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
                            <th>الاسم </th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th> {{ __('lang.occupation_category') }}</th>
                            <th> {{ __('lang.phone_whatsapp') }}</th>
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
                                    <td>
                                        {{ $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name . ' ' . 'القرعاوي' }}
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->occupation_category }}</td>
                                    <td>{{ $user->phone_whatsapp }}</td>
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
                                            data-bs-target="#kt_modal_password">
                                            تغيير كلمة المرور
                                        </button>
                                        @include('pages/dashboards/admin/_edit_password')
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div
                        class="card-footer flex flex-col md:flex-row gap-5 justify-center md:justify-between text-gray-600 text-sm font-medium">
                        @if ($users->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation"
                                class="flex items-center justify-between w-full">
                                {{-- Small screens: Previous & Next buttons --}}
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <a href="{{ $users->previousPageUrl() }}"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:ring focus:ring-gray-300 {{ $users->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        « Previous
                                    </a>
                                    <a href="{{ $users->nextPageUrl() }}"
                                        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:ring focus:ring-gray-300 {{ $users->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        Next »
                                    </a>
                                </div>

                                {{-- Large screens: Pagination --}}
                                <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">{{ $users->firstItem() }}</span>
                                        to <span class="font-medium">{{ $users->lastItem() }}</span>
                                        of <span class="font-medium">{{ $users->total() }}</span> results
                                    </p>

                                    {{-- Pagination Controls --}}
                                    <div class="inline-flex items-center shadow-sm rounded-md">
                                        {{-- Previous button --}}
                                        <a href="{{ $users->previousPageUrl() }}"
                                            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:text-gray-400 focus:ring focus:ring-gray-300 {{ $users->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            «
                                        </a>

                                        {{-- Page Numbers (Centered) --}}
                                        @foreach ($users->links()->elements[0] as $page => $url)
                                            <a href="{{ $url }}"
                                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium border border-gray-300 {{ $page == $users->currentPage() ? 'bg-gray-200 text-gray-500 cursor-default' : 'bg-white text-gray-700 hover:text-gray-500' }}">
                                                {{ $page }}
                                            </a>
                                        @endforeach

                                        {{-- Next button --}}
                                        <a href="{{ $users->nextPageUrl() }}"
                                            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:text-gray-400 focus:ring focus:ring-gray-300 {{ $users->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                            »
                                        </a>
                                    </div>
                                </div>
                            </nav>
                        @endif
                    </div>

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
                $('.change-password-btn').click(function() {
                    $('#user-id').val($(this).data('user-id'));
                });

                $('#change-password-form').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('changePasswordByAdmin') }}',
                        data: $(this).serialize(),
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم تغيير كلمة المرور بنجاح',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                $('#kt_modal_password').modal('hide');
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMsg = 'حدث خطأ ما';
                            if (xhr.status === 404) errorMsg = 'المستخدم غير موجود';
                            else if (xhr.status === 400) errorMsg = 'كلمة المرور القديمة غير صحيحة';

                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: errorMsg
                            });
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
