<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1"> الدورات الاستشارية </span>
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
                            @foreach ($courses as $course)
                                <tr>
                                    <td>{{ $course->user->first_name . ' ' . $course->user->last_name }}</td>
                                    <td>
                                        {{ substr($course->description, 0, 100) }}
                                        @if (strlen($course->description) > 100)
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#projectModal{{ $course->id }}">....</a>
                                        @endif
                                        <!-- Modal -->
                                        <div class="modal fade" id="projectModal{{ $course->id }}" tabindex="-1"
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
                                                        {{ $course->description }}
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
                                        {{ $course->likes()->count() }}
                                    </td>
                                    <td>
                                        {{ $course->favorites()->count() }}
                                    </td>
                                    <td>
                                        <button class="delete-course-btn btn btn-danger btn-active-color-dark me-1"
                                            data-course-id="{{ $course->id }}">
                                            <i class="ki-duotone ki-tablet-delete">
                                                <i class="path1"></i>
                                                <i class="path2"></i>
                                                <i class="path3"></i>
                                            </i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#statusModal{{ $course->id }}"> Change Status </button>
                                    </td>
                                </tr>
                                <!-- Modal -->
                                <div class="modal fade" id="statusModal{{ $course->id }}" tabindex="-1"
                                    aria-labelledby="statusModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="statusModalLabel">Change course Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form id="statusForm{{ $course->id }}" method="POST"
                                                action="{{ route('courses.changeStatus', $course->id) }}"> @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group"> <label for="status">Status</label>
                                                        <select id="status" name="status" class="form-control">
                                                            <option value="private"
                                                                {{ $course->status == 'private' ? 'selected' : '' }}>
                                                                Private</option>
                                                            <option value="public"
                                                                {{ $course->status == 'public' ? 'selected' : '' }}>
                                                                Public
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">

                                                    <button type="submit" class="btn btn-primary">Update
                                                        Status</button>
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
                        @if ($courses->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation"
                                class="flex items-center justify-between w-full">
                                {{-- Small screens: Previous & Next buttons --}}
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <a href="{{ $courses->previousPageUrl() }}"
                                        class="pagination-btn {{ $courses->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        « Previous
                                    </a>
                                    <a href="{{ $courses->nextPageUrl() }}"
                                        class="pagination-btn {{ $courses->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        Next »
                                    </a>
                                </div>

                                {{-- Large screens: Pagination details and numbered links --}}
                                <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">{{ $courses->firstItem() }}</span>
                                        to <span class="font-medium">{{ $courses->lastItem() }}</span>
                                        of <span class="font-medium">{{ $courses->total() }}</span> results
                                    </p>

                                    {{-- Pagination controls --}}
                                    <div class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                                        {{-- Previous button --}}
                                        <a href="{{ $courses->previousPageUrl() }}"
                                            class="pagination-btn {{ $courses->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            «
                                        </a>

                                        {{-- Page numbers --}}
                                        @foreach ($courses->links()->elements[0] as $page => $url)
                                            <a href="{{ $url }}"
                                                class="pagination-btn {{ $page == $courses->currentPage() ? 'bg-gray-200 text-gray-500 cursor-default' : '' }}">
                                                {{ $page }}
                                            </a>
                                        @endforeach

                                        {{-- Next button --}}
                                        <a href="{{ $courses->nextPageUrl() }}"
                                            class="pagination-btn {{ $courses->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
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
                $('.delete-course-btn').click(function() {
                    const courseId = $(this).data('course-id');
                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد أنك تريد حذف هذه الدورة؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احذفها',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'courses/' + courseId,
                                type: 'DELETE',
                                dataType: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    console.log(response.message);
                                    Swal.fire({
                                        title: 'تم الحذف',
                                        text: 'تم حذف الدورة.',
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
        </script>
    @endsection
</x-default-layout>
