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
                                    <td>
                                        {{ substr($job->description, 0, 100) }}
                                        @if (strlen($job->description) > 100)
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#projectModal{{ $job->id }}">....</a>
                                        @endif
                                        <!-- Modal -->
                                        <div class="modal fade" id="projectModal{{ $job->id }}" tabindex="-1"
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
                                                        {{ $job->description }}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">الغاء</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Job Location -->
                                    <td>
                                        {{ optional($job->region)->name . ' ' . optional($job->city)->name . ' ' . optional($job->neighborhood)->name }}
                                    </td>

                                    <!-- Company Name -->
                                    <td>{{ $job->company_name ?? 'N/A' }}</td>

                                    <!-- Company Description -->
                                    <td>{{ $job->company_description ?? 'N/A' }}</td>

                                    <!-- Company Location -->
                                    <td>
                                        {{ optional($job->companyRegion)->name . ' ' . optional($job->companyCity)->name . ' ' . optional($job->companyNeighborhood)->name }}
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
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#statusModal{{ $job->id }}"> Change Status </button>
                                    </td>
                                </tr>
                                <!-- Modal -->
                                <div class="modal fade" id="statusModal{{ $job->id }}" tabindex="-1"
                                    aria-labelledby="statusModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="statusModalLabel">Change Job Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form id="statusForm{{ $job->id }}" method="POST"
                                                action="{{ route('jobs.changeStatus', $job->id) }}"> @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group"> <label for="status">Status</label>
                                                        <select id="status" name="status" class="form-control">
                                                            <option value="private"
                                                                {{ $job->status == 'private' ? 'selected' : '' }}>
                                                                Private</option>
                                                            <option value="public"
                                                                {{ $job->status == 'public' ? 'selected' : '' }}>Public
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
