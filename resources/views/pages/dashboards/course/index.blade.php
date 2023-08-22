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
                                    <td>{{ $course->description }}</td>

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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
