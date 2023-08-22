<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">الوظائف</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th> مستخدم تم انشاء </th>
                            <th> وصف </th>
                            <th> العنوان </th>
                            <th>اسم الشركة</th>
                            <th>وصف الشركة </th>
                            <th> عنوان الشركة </th>
                            <th> عدد الاعجاب </th>
                            <th> عدد المفضل </th>
                            <th> عدد المقدمين </th>
                            <th> الادارة </th>

                        </thead>
                        <tbody>
                            @foreach ($jobs as $job)
                                <tr>
                                    <td>{{ $job->user->first_name . ' ' . $job->user->last_name }}</td>
                                    <td>{{ $job->description }}</td>
                                    <td>
                                        {{ ($job->region ? $job->region->name : '') . ' ' . ($job->city ? $job->city->name : '') . ' ' . ($job->neighborhood ? $job->neighborhood->name : '') }}
                                    </td>
                                    <td>{{ $job->company_name }}</td>
                                    <td>{{ $job->company_description }}</td>
                                    <td>
                                        {{ ($job->companyRegion ? $job->companyRegion->name : '') . ' ' . ($job->companyCity ? $job->companyCity->name : '') . ' ' . ($job->companyNeighborhood ? $job->companyNeighborhood->name : '') }}
                                    </td>
                                    <td>
                                        {{ $job->likes()->count() }}
                                    </td>
                                    <td>
                                        {{ $job->favorites()->count() }}
                                    </td>
                                    <td>{{ $job->jobApplications()->count() }}</td>
                                    <td>
                                        <button class="delete-job-btn btn btn-danger btn-active-color-dark  me-1"
                                            data-job-id="{{ $job->id }}">
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
                $('.delete-job-btn').click(function() {
                    const jobId = $(this).data('job-id');
                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد أنك تريد حذف هذه الوظيفة؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احذفه',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'jobs/' + jobId,
                                type: 'DELETE',
                                dataType: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    console.log(response.message);
                                    Swal.fire({
                                        title: 'تم الحذف',
                                        text: 'تم حذف الوظيفة.',
                                        icon: 'success'
                                    }).then(() => {
                                        // Reload the page after successful delete
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
