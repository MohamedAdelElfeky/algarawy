<x-default-layout>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">الدعم الفني</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <form method="post" action="{{ route('addOrUpdateSupport') }}">
                    @csrf
                    <div class="row">
                        <div class="mb-4">
                            <label for="number" class="form-label">رقم الدعم الفني:</label>
                            <input type="text" class="form-control" id="number" name="number"
                                value="{{ $number ?? '' }}" required>
                        </div>
                        <div class="mb-4 hidden">
                            <label for="number" class="form-label">
                                بريد الالكتروني لدعم كلمة السر
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ $email ?? '' }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">أضافة / تعديل </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @section('script')
        <script></script>
    @endsection
</x-default-layout>
