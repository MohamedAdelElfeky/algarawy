<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">المشاريع</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th> مستخدم تم انشاء </th>
                            <th> وصف </th>
                            <th> عدد الاعجاب </th>
                            <th> عدد المفضل </th>
                            <th> الادارة </th>

                        </thead>
                        <tbody>
                            @foreach ($meetings as $meeting)
                                <tr>
                                    <td>{{ $meeting->user->first_name . ' ' . $meeting->user->last_name }} </td>

                                    <td>
                                        {{ substr($meeting->description, 0, 100) }}
                                        @if (strlen($meeting->description) > 100)
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#meetingModal{{ $meeting->id }}">....</a>
                                        @endif
                                        <!-- Modal -->
                                        <div class="modal fade" id="meetingModal{{ $meeting->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="meetingModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="meetingModalLabel">
                                                            عرض الوصف </h5>
                                                        <button type="button"
                                                            class="btn btn-sm btn-icon btn-active-color-primary"
                                                            data-bs-dismiss="modal">
                                                            X
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{ $meeting->description }}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">الغاء</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $meeting->likes()->count() }}
                                    </td>
                                    <td>
                                        {{ $meeting->favorites()->count() }}
                                    </td>

                                    <td>
                                        <button
                                            class="delete-meeting-btn btn btn-icon btn-color-light btn-bg-danger btn-active-color-dark me-1"
                                            data-meeting-id="{{ $meeting->id }}">
                                            <i class="ki-duotone ki-tablet-delete">
                                                <i class="path1"></i>
                                                <i class="path2"></i>
                                                <i class="path3"></i>
                                            </i>
                                        </button>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#statusModal{{ $meeting->id }}"> تغيير الحالة </button>

                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#approvalModal{{ $meeting->id }}">تغيير الموافقة</button>

                                    </td>
                                </tr>

                                <div class="modal fade" id="statusModal{{ $meeting->id }}" tabindex="-1"
                                    aria-labelledby="statusModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="statusModalLabel"> تغيير الحالة </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form id="statusForm{{ $meeting->id }}" method="POST"
                                                action="{{ route('visibility.update', ['model' => 'meeting', 'id' => $meeting->id]) }}"
                                                class="visibilityForm">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group"> <label for="status">الحالة</label>
                                                        <select id="status" name="status" class="form-control">
                                                            <option value="private"
                                                                {{ optional($meeting->visibility)->status == 'private' ? 'selected' : '' }}>
                                                                خاص </option>
                                                            <option value="public"
                                                                {{ optional($meeting->visibility)->status == 'public' ? 'selected' : '' }}>
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

                                <div class="modal fade" id="approvalModal{{ $meeting->id }}" tabindex="-1"
                                    aria-labelledby="approvalModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"> تغيير الحالة </h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST"
                                                action="{{ route('approve.update', ['model' => 'meeting', 'id' => $meeting->id]) }}"
                                                class="approvalForm">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <select name="status" class="form-control">
                                                        <option value="pending"
                                                            {{ optional($meeting->approval)->status == 'pending' ? 'selected' : '' }}>
                                                            في انتظار الموافقة </option>
                                                        <option value="approved"
                                                            {{ optional($meeting->approval)->status == 'approved' ? 'selected' : '' }}>
                                                            موافقة </option>
                                                        <option value="rejected"
                                                            {{ optional($meeting->approval)->status == 'rejected' ? 'selected' : '' }}>
                                                            مرفوض </option>
                                                    </select>
                                                    <textarea name="notes" class="form-control mt-2" placeholder="Notes (optional)">{{ optional($meeting->approval)->notes }}</textarea>
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
                    @if ($meetings->hasPages())
                        <nav role="navigation" aria-label="Pagination Navigation"
                            class="flex items-center justify-between w-full">
                            {{-- Small screens: Previous & Next buttons --}}
                            <div class="flex justify-between flex-1 sm:hidden">
                                <a href="{{ $meetings->previousPageUrl() }}"
                                    class="pagination-btn {{ $meetings->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    « Previous
                                </a>
                                <a href="{{ $meetings->nextPageUrl() }}"
                                    class="pagination-btn {{ $meetings->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                    Next »
                                </a>
                            </div>

                            {{-- Large screens: Pagination details and numbered links --}}
                            <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ $meetings->firstItem() }}</span>
                                    to <span class="font-medium">{{ $meetings->lastItem() }}</span>
                                    of <span class="font-medium">{{ $meetings->total() }}</span> results
                                </p>

                                {{-- Pagination controls --}}
                                <div class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                                    {{-- Previous button --}}
                                    <a href="{{ $meetings->previousPageUrl() }}"
                                        class="pagination-btn {{ $meetings->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        «
                                    </a>

                                    {{-- Page numbers --}}
                                    @foreach ($meetings->links()->elements[0] as $page => $url)
                                        <a href="{{ $url }}"
                                            class="pagination-btn {{ $page == $meetings->currentPage() ? 'bg-gray-200 text-gray-500 cursor-default' : '' }}">
                                            {{ $page }}
                                        </a>
                                    @endforeach

                                    {{-- Next button --}}
                                    <a href="{{ $meetings->nextPageUrl() }}"
                                        class="pagination-btn {{ $meetings->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
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
                $('.delete-meeting-btn').click(function() {
                    const meetingId = $(this).data('meeting-id');
                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد أنك تريد حذف هذا الاجتماع',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احذفه',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'meetings/' + meetingId,
                                type: 'DELETE',
                                dataType: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    console.log(response.message);
                                    Swal.fire({
                                        title: 'تم الحذف',
                                        text: 'تم حذف الاجتماع.',
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
            });
        </script>
    @endsection
</x-default-layout>
