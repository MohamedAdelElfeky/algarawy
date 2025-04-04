<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">
                        <i class="fas fa-city me-2"></i> <span> المدينة </span>
                    </span>

                </h3>
                <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                    <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_city">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('lang.add') }}</a>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th>الاسم </th>
                            <th>المنطقة </th>
                            <th>{{ __('lang.actions') }} </th>
                        </thead>
                        <tbody>
                            @foreach ($cities as $city)
                                <tr>
                                    <td>{{ $city->name }}</td>
                                    <td>{{ optional($city->region)->name ?? '-' }}</td>
                                    <td>
                                        <button
                                            class="delete-city-btn btn btn-icon btn-color-light btn-bg-danger btn-active-color-dark me-1"
                                            data-city-id="{{ $city->id }}">
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
                        @if ($cities->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation"
                                class="flex items-center justify-between w-full">
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <a href="{{ $cities->previousPageUrl() }}"
                                        class="pagination-btn {{ $cities->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        « {{ __('lang.previous') }}
                                    </a>
                                    <a href="{{ $cities->nextPageUrl() }}"
                                        class="pagination-btn {{ $cities->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        {{ __('lang.next') }} »
                                    </a>
                                </div>

                                {{-- Large screens: Pagination details and numbered links --}}
                                <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                    <p class="text-sm text-gray-700">
                                        {{ __('lang.show') }} <span class="font-medium">{{ $cities->firstItem() }}</span>
                                        {{ __('lang.to') }} <span class="font-medium">{{ $cities->lastItem() }}</span>
                                        {{ __('lang.of') }} <span class="font-medium">{{ $cities->total() }}</span> {{ __('lang.results') }}
                                    </p>

                                    {{-- Pagination controls --}}
                                    <div class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                                        {{-- Previous button --}}
                                        <a href="{{ $cities->previousPageUrl() }}"
                                            class="pagination-btn {{ $cities->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            «
                                        </a>

                                        {{-- Page numbers --}}
                                        @foreach ($cities->links()->elements[0] as $page => $url)
                                            <a href="{{ $url }}"
                                                class="pagination-btn {{ $page == $cities->currentPage() ? 'bg-gray-200 text-gray-500 cursor-default' : '' }}">
                                                {{ $page }}
                                            </a>
                                        @endforeach

                                        {{-- Next button --}}
                                        <a href="{{ $cities->nextPageUrl() }}"
                                            class="pagination-btn {{ $cities->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
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
    @include('pages/dashboards/cities/_add')

    @section('script')
        <script>
            $(document).ready(function() {
                $('#cityForm').submit(function(e) {
                    e.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        type: 'post',
                        url: '{{ route('cities.store') }}',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'نجاح!',
                                text: 'تمت إضافة المدينة بنجاح.',
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
                                text: 'فشل إضافة المدينة.',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });
                });
            });

            $(document).ready(function() {
                $('.delete-city-btn').click(function() {
                    var cityId = $(this).data('city-id');
                    Swal.fire({
                        title: 'هل أنت متأكد؟',
                        text: 'سيتم حذف هذه المدينة نهائياً!',
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
                                url: 'cities/' + cityId,
                                type: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: 'تم الحذف!',
                                        text: 'تم حذف المدينة بنجاح.',
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
