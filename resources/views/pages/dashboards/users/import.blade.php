<x-default-layout>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <!-- Optimized Header -->
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">📂 Import Data</h4>
                        <small class="text-light">Upload an Excel file (.xlsx, .xls, .csv) to import data</small>
                    </div>

                    <div class="card-body">
                        {{-- id, first_name, last_name, email, phone, national_id, password, birth_date, location, region_id, city_id, neighborhood_id, created_at, updated_at, national_card_image_front, national_card_image_back, avatar, card_images --}}

                     
                        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- File Input -->
                            <div class="mb-3">
                                <label for="file" class="form-label fw-bold">Select Excel File:</label>
                                <a href="{{ route('download.example') }}" class="btn btn-info btn-sm">⬇️ Download Example File</a>

                                <input type="file" name="file" id="file"
                                    class="form-control @error('file') is-invalid @enderror" accept=".xlsx, .xls, .csv"
                                    required>
                                <small class="text-muted">Supported formats: .xlsx, .xls, .csv</small>

                                <!-- Validation Error Message -->
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">📥 Upload & Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
