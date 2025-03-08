<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">
                        <i class="fas fa-users me-2"></i> {{ __('lang.meetings') }}
                    </span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th> {{ __('lang.created_by') }}</th>
                            <th> {{ __('lang.created_at') }}</th>
                            <th> {{ __('lang.name') }}</th>
                            <th> {{ __('lang.description') }} </th>
                            <th> {{ __('lang.link') }}</th>
                            <th> {{ __('lang.start_time') }}</th>
                            <th> {{ __('lang.end_time') }}</th>
                            <th> {{ __('lang.type') }}</th>
                            <th>{{ __('lang.count_likes') }}</th>
                            <th> {{ __('lang.count_favorite') }}</th>
                            <th> {{ __('lang.count_complaints') }}</th>
                            <th>{{ __('lang.change_approval') }}</th>
                            <th> {{ __('lang.change_visibility') }}</th>
                            <th> {{ __('lang.delete') }} </th>

                        </thead>
                        <tbody>
                            @foreach ($meetings as $meeting)
                                <tr>
                                    <td>{{ $meeting->user->first_name . ' ' . $meeting->user->last_name }} </td>
                                    <td>{{ $meeting->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $meeting->name }}</td>

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
                                        @if ($meeting->link)
                                            <a href="{{ $meeting->link }}" class="btn btn-primary" target="_blank">
                                                {{ __('lang.view') }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('lang.no_link_available') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $meeting->start_time ? $meeting->start_time->format('Y-m-d') : ' ' }}
                                    </td>
                                    <td>
                                        {{ $meeting->end_time ? $meeting->end_time->format('Y-m-d') : ' ' }}
                                    </td>

                                    <td>
                                        {{ $meeting->type ? __('lang.' . $meeting->type) : ' ' }}
                                    </td>
                                    <td> {{ $meeting->likes()->count() }} </td>
                                    <td> {{ $meeting->favorites()->count() }} </td>
                                    <td>{{ $meeting->complaints()->count() }}</td>

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
                                                $approvalColors[optional($meeting->approval)->status] ??
                                                'btn-warning text-dark';
                                            $approvalText =
                                                $approvalNames[optional($meeting->approval)->status] ??
                                                __('lang.pending');
                                        @endphp
                                        <button type="button" class="btn {{ $approvalClass }} w-100"
                                            data-bs-toggle="modal" data-bs-target="#approvalModal{{ $meeting->id }}">
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
                                                $statusColors[optional($meeting->visibility)->status] ??
                                                'btn-secondary';
                                            $statusText =
                                                $statusNames[optional($meeting->visibility)->status] ??
                                                __('lang.private');
                                        @endphp
                                        <button type="button" class="btn {{ $statusClass }} w-100"
                                            data-bs-toggle="modal" data-bs-target="#statusModal{{ $meeting->id }}">
                                            {{ $statusText }}
                                        </button>
                                    </td>

                                    <td>
                                        <button
                                            class="delete-meeting-btn btn btn-icon btn-color-light btn-bg-danger btn-active-color-dark me-1"
                                            data-meeting-id="{{ $meeting->id }}">
                                            {{ __('lang.delete') }}
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="statusModal{{ $meeting->id }}" tabindex="-1"
                                    aria-labelledby="statusModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="statusModalLabel">
                                                    {{ __('lang.change_visibility') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form id="statusForm{{ $meeting->id }}" method="POST"
                                                action="{{ route('visibility.update', ['model' => 'meeting', 'id' => $meeting->id]) }}"
                                                class="visibilityForm">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group"> <label
                                                            for="status">{{ __('lang.status') }}</label>
                                                        <select id="status" name="status" class="form-control">
                                                            <option value="private"
                                                                {{ optional($meeting->visibility)->status == 'private' ? 'selected' : '' }}>
                                                                {{ __('lang.private') }} </option>
                                                            <option value="public"
                                                                {{ optional($meeting->visibility)->status == 'public' ? 'selected' : '' }}>
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

                                <div class="modal fade" id="approvalModal{{ $meeting->id }}" tabindex="-1"
                                    aria-labelledby="approvalModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('lang.change_approval') }}</h5>
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
                                                            {{ __('lang.pending') }} </option>
                                                        <option value="approved"
                                                            {{ optional($meeting->approval)->status == 'approved' ? 'selected' : '' }}>
                                                            {{ __('lang.approved') }} </option>
                                                        <option value="rejected"
                                                            {{ optional($meeting->approval)->status == 'rejected' ? 'selected' : '' }}>
                                                            {{ __('lang.rejected') }} </option>
                                                    </select>
                                                    <textarea name="notes" class="form-control mt-2" placeholder="Notes (optional)">{{ optional($meeting->approval)->notes }}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('lang.update') }}
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
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <a href="{{ $meetings->previousPageUrl() }}"
                                        class="pagination-btn {{ $meetings->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        « {{ __('lang.previous') }}
                                    </a>
                                    <a href="{{ $meetings->nextPageUrl() }}"
                                        class="pagination-btn {{ $meetings->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        {{ __('lang.next') }} »
                                    </a>
                                </div>

                                {{-- Large screens: Pagination details and numbered links --}}
                                <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                                    <p class="text-sm text-gray-700">
                                        {{ __('lang.show') }} <span
                                            class="font-medium">{{ $meetings->firstItem() }}</span>
                                        {{ __('lang.to') }} <span
                                            class="font-medium">{{ $meetings->lastItem() }}</span>
                                        {{ __('lang.of') }} <span class="font-medium">{{ $meetings->total() }}</span>
                                        {{ __('lang.results') }}
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
