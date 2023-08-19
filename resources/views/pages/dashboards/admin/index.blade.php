<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">المستخدمين</span>
                </h3>
                <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                    <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_admin">
                        <i class="ki-duotone ki-plus fs-2"></i>أضافة</a>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <th>الاسم الأول</th>
                            <th>الاسم الأخير</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>الموقع</th>
                            <th>الصورة الرمزية</th>
                            <th>صورة البطاقة الأمامية</th>
                            <th>صورة البطاقة الخلفية</th>
                            <th>تاريخ الميلاد</th>
                            <th>الهوية الوطنية</th>
                            {{-- <th> تفعيل المستخدم</th> --}}

                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        @if ($user->region && $user->neighborhood)
                                            {{ $user->region->name . ' ' . $user->region->city . ' ' . $user->neighborhood->name }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ $user->avatar !== null && $user->avatar !== '' ? asset($user->avatar) : asset('default.png') }}"
                                                    alt="" />
                                            </div>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ $user->national_card_image_front !== null && $user->national_card_image_front !== '' ? asset($user->national_card_image_front) : asset('default.png') }}"
                                                    alt="" />


                                            </div>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ $user->national_card_image_back !== null && $user->national_card_image_back !== '' ? asset($user->national_card_image_back) : asset('default.png') }}"
                                                    alt="" />


                                            </div>

                                        </div>
                                    </td>
                                    <td>{{ $user->birth_date }}</td>
                                    <td>{{ $user->national_id }}</td>
                                    {{-- <td>
                                        <button
                                            class="toggle-user-btn btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                            data-user-id="{{ $user->id }}" data-user-active="{{ $user->active }}">
                                            <i class="ki-duotone ki-toggle-on-circle fs-2">
                                                <i class="path1"></i>
                                                <i class="path2"></i>
                                            </i>
                                        </button>
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('pages/dashboards/admin/_add')
    @section('script')
        <script>
            $(document).ready(function() {
                $('.toggle-user-btn').click(function(event) {
                    event.preventDefault();

                    const button = $(this);
                    const userId = button.attr('data-user-id');
                    const isActive = button.attr('data-user-active') === '1';
                    const url = `toggle-user/${userId}`;

                    // Send an AJAX request to the server
                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }, // Include the CSRF token in the headers
                        success: function(data) {
                            if (data.success) {
                                // Toggle the active status in the button data attribute
                                button.attr('data-user-active', isActive ? '0' : '1');
                                // Update button appearance if needed
                            }
                        },
                        error: function(error) {
                            console.error('An error occurred:', error);
                        }
                    });
                });
            });
        </script>
    @endsection
</x-default-layout>
