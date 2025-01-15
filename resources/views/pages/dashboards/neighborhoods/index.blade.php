<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">الحي</span>
                </h3>
                <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                    <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_neighborhood">
                        <i class="ki-duotone ki-plus fs-2"></i>أضافة</a>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th>الاسم</th>
                            <th>المنطقة</th>
                            <th>المدينة</th>
                            <th>الادره</th>
                        </thead>
                        <tbody>
                            @foreach ($neighborhoods as $neighborhood)
                                <tr>
                                    <td>{{ $neighborhood->name }}</td>
                                    <td>{{ optional(optional($neighborhood->city)->region)->name ?? '-' }}</td>
                                    <td>{{ optional($neighborhood->city)->name ?? '-' }}</td>

                                    <td>
                                        <button
                                            class="delete-neighborhood-btn btn btn-icon btn-color-light btn-bg-danger btn-active-color-dark me-1"
                                            data-neighborhood-id="{{ $neighborhood->id }}">
                                            <i class="ki-duotone ki-tablet-delete fs-2">
                                                <i class="path1"></i>
                                                <i class="path2"></i>
                                                <i class="path3"></i>
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('pages/dashboards/neighborhoods/_add')

    @section('script')
        <script>
            $(document).ready(function() {
                $('#neighborhoodForm').submit(function(e) {
                    e.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        type: 'put',
                        url: 'addNeighborhoods',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'نجاح!',
                                text: 'تمت إضافة الحي بنجاح.',
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
                                text: 'فشل إضافة الحي.',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });
                });
            });

            $(document).ready(function() {
                $('.delete-neighborhood-btn').click(function() {
                    var neighborhoodId = $(this).data('neighborhood-id');
                    Swal.fire({
                        title: 'هل أنت متأكد؟',
                        text: 'سيتم حذف هذا الحي نهائياً!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احذفه!',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Perform the AJAX delete request
                            $.ajax({
                                url: 'neighborhoods/' + neighborhoodId,
                                type: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: 'تم الحذف!',
                                        text: 'تم حذف الحي بنجاح.',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(function() {
                                        location.reload();
                                    });
                                    // Handle additional actions if needed
                                },
                                error: function(xhr) {
                                    // Handle error and show error message
                                    Swal.fire({
                                        title: 'خطأ!',
                                        text: 'حدث خطأ أثناء الحذف.',
                                        icon: 'error'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endsection
</x-default-layout>
