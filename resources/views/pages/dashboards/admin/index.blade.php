<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">
                        <i class="fas fa-user-shield me-2"></i> {{ __('lang.admins') }}
                    </span>
                </h3>
                <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                    <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_admin">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('lang.add') }}</a>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th> {{ __('lang.name') }}</th>
                            <th> {{ __('lang.email') }}</th>
                            <th> {{ __('lang.phone') }}</th>
                            <th> {{ __('lang.address') }}</th>
                            <th> {{ __('lang.image_profile') }}</th>
                            <th> {{ __('lang.image_card_from') }}</th>
                            <th> {{ __('lang.image_card_back') }}</th>
                            <th> {{ __('lang.national_id') }}</th>
                            <th> {{ __('lang.action') }}</th>

                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
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
                                        <button class="btn btn-sm btn-primary change-password-btn"
                                            data-user-id="{{ $user->id }}" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_password">
                                            {{ __('lang.change_password') }}
                                        </button>
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
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <a href="{{ $users->previousPageUrl() }}"
                                        class="pagination-btn {{ $users->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        « {{ __('lang.previous') }}
                                    </a>
                                    <a href="{{ $users->nextPageUrl() }}"
                                        class="pagination-btn {{ $users->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        {{ __('lang.next') }} »
                                    </a>
                                </div>

                                {{-- Large screens: Pagination details and numbered links --}}
                                <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                    <p class="text-sm text-gray-700">
                                        {{ __('lang.show') }} <span
                                            class="font-medium">{{ $users->firstItem() }}</span>
                                        {{ __('lang.to') }} <span class="font-medium">{{ $users->lastItem() }}</span>
                                        {{ __('lang.of') }} <span class="font-medium">{{ $users->total() }}</span>
                                        {{ __('lang.results') }}
                                    </p>

                                    {{-- Pagination controls --}}
                                    <div class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                                        {{-- Previous button --}}
                                        <a href="{{ $users->previousPageUrl() }}"
                                            class="pagination-btn {{ $users->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            «
                                        </a>

                                        {{-- Page numbers --}}
                                        @foreach ($users->links()->elements[0] as $page => $url)
                                            <a href="{{ $url }}"
                                                class="pagination-btn {{ $page == $users->currentPage() ? 'bg-gray-200 text-gray-500 cursor-default' : '' }}">
                                                {{ $page }}
                                            </a>
                                        @endforeach

                                        {{-- Next button --}}
                                        <a href="{{ $users->nextPageUrl() }}"
                                            class="pagination-btn {{ $users->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
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
                    <h5 class="modal-title" id="imageModalLabel">{{ __('lang.preview_image') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="{{ __('lang.preview_image') }}">
                </div>
            </div>
        </div>
    </div>
    @include('pages/dashboards/admin/_edit_password')
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
