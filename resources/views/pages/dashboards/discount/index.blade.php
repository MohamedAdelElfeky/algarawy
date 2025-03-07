<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1"> الخصومات والعروض </span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th> مستخدم تم انشاء </th>
                            <th> وصف </th>
                            <th> الخصم </th>
                            <th> السعر </th>
                            <th> عدد الاعجاب </th>
                            <th> عدد المفضل </th>
                            <th> الادارة </th>

                        </thead>
                        <tbody>
                            @foreach ($discounts as $discount)
                                <tr>
                                    <td>{{ $discount->user->first_name . ' ' . $discount->user->last_name }}</td>
                                    <td>
                                        {{ substr($discount->description, 0, 100) }}
                                        @if (strlen($discount->description) > 100)
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#projectModal{{ $discount->id }}">....</a>
                                        @endif
                                        <!-- Modal -->
                                        <div class="modal fade" id="projectModal{{ $discount->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="projectModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="projectModalLabel">
                                                            عرض الوصف </h5>
                                                        <button type="button"
                                                            class="btn btn-sm btn-icon btn-active-color-primary"
                                                            data-bs-dismiss="modal">
                                                            X
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{ $discount->description }}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">الغاء</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $discount->discount }}</td>
                                    <td>{{ $discount->price }}</td>
                                    <td>
                                        {{ $discount->likes()->count() }}
                                    </td>
                                    <td>
                                        {{ $discount->favorites()->count() }}
                                    </td>
                                    <td>
                                        <button class="delete-discount-btn btn btn-danger btn-active-color-dark me-1"
                                            data-discount-id="{{ $discount->id }}">
                                            <i class="ki-duotone ki-tablet-delete">
                                                <i class="path1"></i>
                                                <i class="path2"></i>
                                                <i class="path3"></i>
                                            </i>
                                        </button>

                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#statusModal{{ $discount->id }}"> تغيير الحالة </button>

                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#approvalModal{{ $discount->id }}">تغيير الموافقة</button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="statusModal{{ $discount->id }}" tabindex="-1"
                                    aria-labelledby="statusModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="statusModalLabel"> تغيير الحالة </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form id="statusForm{{ $discount->id }}" method="POST"
                                                action="{{ route('visibility.update', ['model' => 'discount', 'id' => $discount->id]) }}"
                                                class="visibilityForm">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group"> <label for="status">الحالة</label>
                                                        <select id="status" name="status" class="form-control">
                                                            <option value="private"
                                                                {{ optional($discount->visibility)->status == 'private' ? 'selected' : '' }}>
                                                                خاص </option>
                                                            <option value="public"
                                                                {{ optional($discount->visibility)->status == 'public' ? 'selected' : '' }}>
                                                                عام
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">

                                                    <button type="submit" class="btn btn-primary">تحديث الحالة</button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="approvalModal{{ $discount->id }}" tabindex="-1"
                                    aria-labelledby="approvalModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"> تغيير الحالة </h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST"
                                                action="{{ route('approve.update', ['model' => 'discount', 'id' => $discount->id]) }}"
                                                class="approvalForm">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <select name="status" class="form-control">
                                                        <option value="pending"
                                                            {{ optional($discount->approval)->status == 'pending' ? 'selected' : '' }}>
                                                            في انتظار الموافقة </option>
                                                        <option value="approved"
                                                            {{ optional($discount->approval)->status == 'approved' ? 'selected' : '' }}>
                                                            موافقة </option>
                                                        <option value="rejected"
                                                            {{ optional($discount->approval)->status == 'rejected' ? 'selected' : '' }}>
                                                            مرفوض </option>
                                                    </select>
                                                    <textarea name="notes" class="form-control mt-2" placeholder="Notes (optional)">{{ optional($discount->approval)->notes }}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary"> تحديث الحالة
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                    <div
                    class="card-footer flex flex-col md:flex-row gap-5 justify-center md:justify-between text-gray-600 text-sm font-medium">
                    @if ($discounts->hasPages())
                        <nav role="navigation" aria-label="Pagination Navigation"
                            class="flex items-center justify-between w-full">
                            {{-- Small screens: Previous & Next buttons --}}
                            <div class="flex justify-between flex-1 sm:hidden">
                                <a href="{{ $discounts->previousPageUrl() }}"
                                    class="pagination-btn {{ $discounts->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    « Previous
                                </a>
                                <a href="{{ $discounts->nextPageUrl() }}"
                                    class="pagination-btn {{ $discounts->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                    Next »
                                </a>
                            </div>

                            {{-- Large screens: Pagination details and numbered links --}}
                            <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ $discounts->firstItem() }}</span>
                                    to <span class="font-medium">{{ $discounts->lastItem() }}</span>
                                    of <span class="font-medium">{{ $discounts->total() }}</span> results
                                </p>

                                {{-- Pagination controls --}}
                                <div class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                                    {{-- Previous button --}}
                                    <a href="{{ $discounts->previousPageUrl() }}"
                                        class="pagination-btn {{ $discounts->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        «
                                    </a>

                                    {{-- Page numbers --}}
                                    @foreach ($discounts->links()->elements[0] as $page => $url)
                                        <a href="{{ $url }}"
                                            class="pagination-btn {{ $page == $discounts->currentPage() ? 'bg-gray-200 text-gray-500 cursor-default' : '' }}">
                                            {{ $page }}
                                        </a>
                                    @endforeach

                                    {{-- Next button --}}
                                    <a href="{{ $discounts->nextPageUrl() }}"
                                        class="pagination-btn {{ $discounts->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
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
    @section('script')
        <script>
            $(document).ready(function() {
                $('.delete-discount-btn').click(function() {
                    const discountId = $(this).data('discount-id');
                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد أنك تريد حذف هذه الخصم؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احذفه',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'discounts/' + discountId,
                                type: 'DELETE',
                                dataType: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    console.log(response.message);
                                    Swal.fire({
                                        title: 'تم الحذف',
                                        text: 'تم حذف الخصم.',
                                        icon: 'success'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    console.error(xhr.statusText);
                                }
                            });
                        }
                    });
                });
            });


            $(document).ready(function() {
                $('.visibilityForm').submit(function(e) {
                    e.preventDefault();
                    let form = $(this);
                    let actionUrl = form.attr('action');

                    $.ajax({
                        url: actionUrl,
                        type: 'PUT',
                        data: form.serialize(),
                        success: function(response) {
                            Swal.fire({
                                title: 'تم بنجاح',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Something went wrong!',
                                icon: 'error'
                            });
                        }
                    });
                });
            });
            $(document).ready(function() {
                $('.approvalForm').submit(function(e) {
                    e.preventDefault();
                    let form = $(this);
                    let actionUrl = form.attr('action');

                    $.ajax({
                        url: actionUrl,
                        type: 'PUT',
                        data: form.serialize(),
                        success: function(response) {
                            Swal.fire({
                                title: 'تم بنجاح',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Something went wrong!',
                                icon: 'error'
                            });
                        }
                    });
                });
            });
        </script>
    @endsection
</x-default-layout>
