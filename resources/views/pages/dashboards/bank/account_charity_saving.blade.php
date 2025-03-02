<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">حساب الصدقة والزكاة</span>
                </h3>
                <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                    <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_charity_saving">
                        <i class="ki-duotone ki-plus fs-2"></i>أضافة</a>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th> رقم حساب </th>
                            <th> IBAN </th>
                            <th> اسم البنك </th>
                            <th> Swift Number </th>
                            <th> نوع الحساب </th>
                            <th> الحالة </th>
                            <th> إجراء </th>
                        </thead>
                        <tbody>
                            @foreach ($banks as $bank)
                                <tr>
                                    <td>{{ $bank->account_number }}</td>
                                    <td>{{ $bank->iban }}</td>
                                    <td>{{ $bank->bank_name }}</td>
                                    <td>{{ $bank->swift_number }}</td>
                                    <td>
                                        @if ($bank->type === 'saving')
                                            الادخار
                                        @elseif ($bank->type === 'charity')
                                            الاستثمار
                                        @endif
                                    </td>
                                    <td>
                                        @if ($bank->status === 'active')
                                            مفعل
                                        @else
                                            غير مفعل
                                        @endif
                                    </td>
                                    <td>
                                        @if ($bank->status === 'inactive')
                                            <button
                                                class="btn btn-bg-primary btn-active-color-white btn-sm me-1 activate-btn"
                                                data-id="{{ $bank->id }}">تفعيل</button>
                                        @else
                                            <button
                                                class="btn btn-bg-danger btn-active-color-white btn-sm me-1 deactivate-btn"
                                                data-id="{{ $bank->id }}">إلغاء
                                                التفعيل</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div
                        class="card-footer flex flex-col md:flex-row gap-5 justify-center md:justify-between text-gray-600 text-sm font-medium">
                        @if ($banks->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation"
                                class="flex items-center justify-between w-full">
                                {{-- Small screens: Previous & Next buttons --}}
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <a href="{{ $banks->previousPageUrl() }}"
                                        class="pagination-btn {{ $banks->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        « Previous
                                    </a>
                                    <a href="{{ $banks->nextPageUrl() }}"
                                        class="pagination-btn {{ $banks->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        Next »
                                    </a>
                                </div>

                                {{-- Large screens: Pagination details and numbered links --}}
                                <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">{{ $banks->firstItem() }}</span>
                                        to <span class="font-medium">{{ $banks->lastItem() }}</span>
                                        of <span class="font-medium">{{ $banks->total() }}</span> results
                                    </p>

                                    {{-- Pagination controls --}}
                                    <div class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                                        {{-- Previous button --}}
                                        <a href="{{ $banks->previousPageUrl() }}"
                                            class="pagination-btn {{ $banks->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            «
                                        </a>

                                        {{-- Page numbers --}}
                                        @foreach ($banks->links()->elements[0] as $page => $url)
                                            <a href="{{ $url }}"
                                                class="pagination-btn {{ $page == $banks->currentPage() ? 'bg-gray-200 text-gray-500 cursor-default' : '' }}">
                                                {{ $page }}
                                            </a>
                                        @endforeach

                                        {{-- Next button --}}
                                        <a href="{{ $banks->nextPageUrl() }}"
                                            class="pagination-btn {{ $banks->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
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
    @include('pages/dashboards/bank/_add_charity_saving')
    @section('script')
        <script>
            $(document).ready(function() {
                // Set CSRF token for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('.activate-btn').click(function() {
                    let accountId = $(this).data('id');
                    $.post(`banks/activate/${accountId}`, function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم التفعيل',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            location.reload();
                        });
                    });
                });

                $('.deactivate-btn').click(function() {
                    let accountId = $(this).data('id');
                    $.post(`banks/deactivate/${accountId}`, function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم إلغاء التفعيل',
                            text: data.message,
                            confirmButtonText: 'حسناً'
                        }).then(function() {
                            location.reload();
                        });
                    });
                });
            });

            $(document).ready(function() {
                $('#bankAccountForm').submit(function(e) {
                    e.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'bank-accounts',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'نجاح!',
                                text: 'تمت إضافة الحساب المصرفي بنجاح.',
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
                                text: 'فشل إضافة حساب مصرفي.',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });
                });
            });
        </script>
    @endsection
</x-default-layout>
