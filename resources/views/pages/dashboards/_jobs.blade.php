<div class="row g-5 g-xxl-8">
    @foreach ($jobData as $item)
        <div class="col-xl-6">
            <div class="card mb-5 mb-xxl-8">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="symbol symbol-45px me-5">
                                <img src="{{ $item['user']['avatar'] ? asset($item['user']['avatar']) : asset('assets/media/avatars/blank.png') }}"
                                    alt="" />
                            </div>
                            <div class="d-flex flex-column me-5">
                                <a href="#" class="text-gray-900 text-hover-primary fs-6 fw-bold">
                                    {{ $item['company_name'] }} </a>
                            </div>
                        </div>

                        <div class="my-0">
                            <button type="button"
                                class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                {{ $item['count_apply_job'] ? $item['count_apply_job'] : '0' }} متقدم
                            </button>
                        </div>
                    </div>

                    <div class="mb-7">
                        <div class="text-gray-800 mb-5">
                            {{ $item['company_description'] }}
                        </div>

                        <img src="{{ $item['company_logo'] ? asset($item['company_logo']) : asset('assets/media/stock/900x600/51.jpg') }}"
                            alt="Job Photo" class="img-fluid mb-4">

                        <div class="d-flex align-items-center mb-5">
                            <div class="d-flex align-items-center mb-5">
                                @if ($item['is_training'])
                                    <a href="#"
                                        class="btn btn-sm btn-light btn-color-muted btn-active-light-info px-4 py-2 me-4">
                                        تدريب
                                    </a>
                                @else
                                    @if ($item['job_duration'] == 'full_time')
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-color-muted btn-active-light-success px-4 py-2 me-4">
                                            دوام كامل
                                        </a>
                                    @elseif ($item['job_duration'] == 'part_time')
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-color-muted btn-active-light-success px-4 py-2 me-4">
                                            دوام جزئي
                                        </a>
                                    @endif
                                    <a href="#"
                                        class="btn btn-sm btn-light btn-color-muted btn-active-light-danger px-4 py-2 me-4">
                                        {{ $item['price'] }} ريال
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="mb-7" style="float: left;">
                        <div class="d-flex align-items-center mb-5">
                            <div class="d-flex align-items-center mb-5">
                                <!-- Like Button -->
                                <a href="#"
                                    class="btn btn-sm {{ $item['like'] ? 'btn-primary' : 'btn-light btn-color-muted' }} px-4 py-2 me-4">
                                    <i class="far fa-thumbs-up"></i> Like
                                </a>

                                <!-- Favorite Button -->
                                <a href="#"
                                    class="btn btn-sm {{ $item['favorite'] ? 'btn-info' : 'btn-light btn-color-muted' }} px-4 py-2 me-4">
                                    <i class="far fa-heart"></i> Favorite
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="separator mb-4"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>
