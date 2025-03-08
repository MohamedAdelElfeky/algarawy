<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1"> 
                        <i class="fas fa-id-card me-2"></i> قائمة العضويات </span>
                </h3>
                <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                    <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_membership">
                        <i class="ki-duotone ki-plus fs-2"></i>أضافة</a>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>النقاط المطلوبة</th>
                            <th>المزايا</th>
                            <th>الإجراءات</th>
                        </thead>
                        <tbody>
                            @foreach ($memberships as $membership)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $membership->name }}</td>
                                    <td>{{ $membership->points_required }}</td>
                                    <td>{{ $membership->benefits ?? 'لا يوجد' }}</td>
                                    <td>
                                        <button
                                            class="delete-membership-btn btn btn-icon btn-color-light btn-bg-danger btn-active-color-dark me-1"
                                            data-membership-id="{{ $membership->id }}">
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
                    <div
                        class="card-footer flex flex-col md:flex-row gap-5 justify-center md:justify-between text-gray-600 text-sm font-medium">
                        @if ($memberships->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation"
                                class="flex items-center justify-between w-full">
                                {{-- Small screens: Previous & Next buttons --}}
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <a href="{{ $memberships->previousPageUrl() }}"
                                        class="pagination-btn {{ $memberships->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        « Previous
                                    </a>
                                    <a href="{{ $memberships->nextPageUrl() }}"
                                        class="pagination-btn {{ $memberships->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        Next »
                                    </a>
                                </div>

                                {{-- Large screens: Pagination details and numbered links --}}
                                <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">{{ $memberships->firstItem() }}</span>
                                        to <span class="font-medium">{{ $memberships->lastItem() }}</span>
                                        of <span class="font-medium">{{ $memberships->total() }}</span> results
                                    </p>

                                    {{-- Pagination controls --}}
                                    <div class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                                        {{-- Previous button --}}
                                        <a href="{{ $memberships->previousPageUrl() }}"
                                            class="pagination-btn {{ $memberships->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            «
                                        </a>

                                        {{-- Page numbers --}}
                                        @foreach ($memberships->links()->elements[0] as $page => $url)
                                            <a href="{{ $url }}"
                                                class="pagination-btn {{ $page == $memberships->currentPage() ? 'bg-gray-200 text-gray-500 cursor-default' : '' }}">
                                                {{ $page }}
                                            </a>
                                        @endforeach

                                        {{-- Next button --}}
                                        <a href="{{ $memberships->nextPageUrl() }}"
                                            class="pagination-btn {{ $memberships->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
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
    @include('pages/dashboards/memberships/_add')

    @section('script')
        <script>
            $(document).ready(function() {
                $('#membershipForm').submit(function(e) {
                    e.preventDefault();
                    var formData = $(this).serialize();

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('memberships.store') }}', // Correct route
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'نجاح!',
                                text: response.message,
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
                                text: 'فشل إضافة العضوية.',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });
                });
            });


            $(document).ready(function() {
                $('.delete-membership-btn').click(function() {
                    var membershipId = $(this).data('membership-id');
                    Swal.fire({
                        title: 'هل أنت متأكد؟',
                        text: 'سيتم حذف هذه العضوية نهائياً!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احذفها!',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Perform the AJAX delete request
                            $.ajax({
                                url: 'memberships/' + membershipId,
                                type: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: 'تم الحذف!',
                                        text: response.message,
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(function() {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
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
