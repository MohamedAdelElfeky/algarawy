<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">صندوق الادخار والاستثمار</span>
                </h3>
                <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                    <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_investment">
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
                </div>
            </div>
        </div>
    </div>
    @include('pages/dashboards/bank/_add_investment')
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
                // function refreshTable() {
                //     $.get('/banks', function(data) {
                //         $('.table tbody').html(data);
                //     });
                // }
            });
        </script>
    @endsection
</x-default-layout>
