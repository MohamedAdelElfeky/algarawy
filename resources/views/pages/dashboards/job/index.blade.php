<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">
                        <i class="fas fa-briefcase me-2"></i> {{ __('lang.jobs') }}
                    </span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th> {{ __('lang.created_by') }}</th>
                            <th> وصف </th>
                            <th> العنوان </th>
                            <th>اسم الشركة</th>
                            <th>وصف الشركة </th>
                            <th> عنوان الشركة </th>
                            <th>{{ __('lang.count_likes') }}</th>
                            <th> {{ __('lang.count_favorite') }}</th>
                            <th> {{ __('lang.count_complaints') }}</th>
                            <th> {{ __('lang.count_applicants') }}</th>
                            <th>{{ __('lang.change_approval') }}</th>
                            <th> {{ __('lang.change_visibility') }}</th>
                            <th> {{ __('lang.delete') }} </th>

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

                                    <td>{{ optional($job->JobCompanies)->name }}</td>

                                    <td>{{ optional($job->JobCompanies)->description }}</td>
                                    <td>
                                        {{ optional(optional($job->JobCompanies)->region)->name . ' ' . optional(optional($job->JobCompanies)->city)->name . ' ' . optional(optional($job->JobCompanies)->neighborhood)->name }}
                                    </td>

                                    <td>
                                        {{ $job->likes()->count() }}
                                    </td>
                                    <td>
                                        {{ $job->favorites()->count() }}
                                    </td>
                                    <td>
                                        {{ $job->complaints()->count() }}
                                    </td>
                                    <td>{{ $job->jobApplications()->count() }}</td>
                                    <td class="text-center">
                                        @php
                                            $approvalColors = [
                                                'pending' => 'btn-warning text-dark',
                                                'approved' => 'btn-success',
                                                'rejected' => 'btn-danger',
                                            ];
                                            $approvalNames = [
                                                'pending' => __('lang.pending'),
                                                'approved' => __('lang.approved'),
                                                'rejected' => __('lang.rejected'),
                                            ];
                                            $approvalClass =
                                                $approvalColors[optional($job->approval)->status] ??
                                                'btn-warning text-dark';
                                            $approvalText =
                                                $approvalNames[optional($job->approval)->status] ?? __('lang.pending');
                                        @endphp
                                        <button type="button" class="btn {{ $approvalClass }} w-100"
                                            data-bs-toggle="modal" data-bs-target="#approvalModal{{ $job->id }}">
                                            {{ $approvalText }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusColors = [
                                                'private' => 'btn-secondary',
                                                'public' => 'btn-success',
                                            ];
                                            $statusNames = [
                                                'private' => __('lang.private'),
                                                'public' => __('lang.public'),
                                            ];
                                            $statusClass =
                                                $statusColors[optional($job->visibility)->status] ?? 'btn-secondary';
                                            $statusText =
                                                $statusNames[optional($job->visibility)->status] ?? __('lang.private');
                                        @endphp
                                        <button type="button" class="btn {{ $statusClass }} w-100"
                                            data-bs-toggle="modal" data-bs-target="#statusModal{{ $job->id }}">
                                            {{ $statusText }}
                                        </button>
                                    </td>
                                    <td>
                                        <button class="delete-job-btn btn btn-danger btn-active-color-dark  me-1"
                                            data-job-id="{{ $job->id }}">
                                            {{ __('lang.delete') }}
                                        </button>
                                    </td>
                                    </td>
                                </tr>

                                <div class="modal fade" id="statusModal{{ $job->id }}" tabindex="-1"
                                    aria-labelledby="statusModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="statusModalLabel">
                                                    {{ __('lang.change_visibility') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form id="statusForm{{ $job->id }}" method="POST"
                                                action="{{ route('visibility.update', ['model' => 'job', 'id' => $job->id]) }}"
                                                class="visibilityForm">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group"> <label
                                                            for="status">{{ __('lang.status') }}</label>
                                                        <select id="status" name="status" class="form-control">
                                                            <option value="private"
                                                                {{ optional($job->visibility)->status == 'private' ? 'selected' : '' }}>
                                                                {{ __('lang.private') }} </option>
                                                            <option value="public"
                                                                {{ optional($job->visibility)->status == 'public' ? 'selected' : '' }}>
                                                                {{ __('lang.public') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">

                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('lang.update') }} </button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="approvalModal{{ $job->id }}" tabindex="-1"
                                    aria-labelledby="approvalModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('lang.change_approval') }}</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST"
                                                action="{{ route('approve.update', ['model' => 'job', 'id' => $job->id]) }}"
                                                class="approvalForm">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <select name="status" class="form-control">
                                                        <option value="pending"
                                                            {{ optional($job->approval)->status == 'pending' ? 'selected' : '' }}>
                                                            {{ __('lang.pending') }} </option>
                                                        <option value="approved"
                                                            {{ optional($job->approval)->status == 'approved' ? 'selected' : '' }}>
                                                            {{ __('lang.approved') }} </option>
                                                        <option value="rejected"
                                                            {{ optional($job->approval)->status == 'rejected' ? 'selected' : '' }}>
                                                            {{ __('lang.rejected') }} </option>
                                                    </select>
                                                    <textarea name="notes" class="form-control mt-2" placeholder="Notes (optional)">{{ optional($job->approval)->notes }}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary"> {{ __('lang.update') }}
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
                        @if ($jobs->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation"
                                class="flex items-center justify-between w-full">
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <a href="{{ $jobs->previousPageUrl() }}"
                                        class="pagination-btn {{ $jobs->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        « {{ __('lang.previous') }}
                                    </a>
                                    <a href="{{ $jobs->nextPageUrl() }}"
                                        class="pagination-btn {{ $jobs->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        {{ __('lang.next') }} »
                                    </a>
                                </div>

                                {{-- Large screens: Pagination details and numbered links --}}
                                <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                    <p class="text-sm text-gray-700">
                                        {{ __('lang.show') }} <span
                                            class="font-medium">{{ $jobs->firstItem() }}</span>
                                        {{ __('lang.to') }} <span class="font-medium">{{ $jobs->lastItem() }}</span>
                                        {{ __('lang.of') }} <span class="font-medium">{{ $jobs->total() }}</span>
                                        {{ __('lang.results') }}
                                    </p>

                                    {{-- Pagination controls --}}
                                    <div class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                                        {{-- Previous button --}}
                                        <a href="{{ $jobs->previousPageUrl() }}"
                                            class="pagination-btn {{ $jobs->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            «
                                        </a>

                                        {{-- Page numbers --}}
                                        @foreach ($jobs->links()->elements[0] as $page => $url)
                                            <a href="{{ $url }}"
                                                class="pagination-btn {{ $page == $jobs->currentPage() ? 'bg-gray-200 text-gray-500 cursor-default' : '' }}">
                                                {{ $page }}
                                            </a>
                                        @endforeach

                                        {{-- Next button --}}
                                        <a href="{{ $jobs->nextPageUrl() }}"
                                            class="pagination-btn {{ $jobs->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
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
