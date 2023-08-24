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
                            @foreach ($projects as $project)
                                <tr>
                                    <td>{{ $project->user->first_name . ' ' . $project->user->last_name }} </td>

                                    <td>
                                        {{ substr($project->description, 0, 100) }}
                                        @if (strlen($project->description) > 100)
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#projectModal{{ $project->id }}">....</a>
                                        @endif
                                        <!-- Modal -->
                                        <div class="modal fade" id="projectModal{{ $project->id }}" tabindex="-1"
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
                                                        {{ $project->description }}
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
                                        {{ $project->likes()->count() }}
                                    </td>
                                    <td>
                                        {{ $project->favorites()->count() }}
                                    </td>

                                    <td>
                                        <button class="delete-project-btn btn btn-icon btn-color-light btn-bg-danger btn-active-color-dark me-1"
                                            data-project-id="{{ $project->id }}">
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
                $('.delete-project-btn').click(function() {
                    const projectId = $(this).data('project-id');
                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد أنك تريد حذف هذا المشروع؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احذفه',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'projects/' + projectId,
                                type: 'DELETE',
                                dataType: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    console.log(response.message);
                                    Swal.fire({
                                        title: 'تم الحذف',
                                        text: 'تم حذف المشروع.',
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
