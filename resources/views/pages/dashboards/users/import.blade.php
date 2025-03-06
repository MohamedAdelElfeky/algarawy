<x-default-layout>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-gradient text-white text-center rounded-top-4"
                        style="background: linear-gradient(135deg, #0078D7, #00A4EF);">
                        <h4 class="mb-1"><i class="fas fa-file-import"></i> استيراد البيانات</h4>
                        <small class="text-primary">قم برفع ملف Excel (.xlsx, .xls, .csv) لاستيراد البيانات</small>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label for="file" class="form-label fw-bold text-dark"><i
                                        class="fas fa-file-excel text-success"></i> اختر ملف Excel:</label>

                                <div class="input-group">
                                    <input type="file" name="file" id="file"
                                        class="form-control @error('file') is-invalid @enderror"
                                        accept=".xlsx, .xls, .csv" required>
                                    <button class="btn btn-secondary" type="button"
                                        onclick="window.location.href='{{ route('download.example') }}'">
                                        ⬇️ تحميل ملف مثال
                                    </button>
                                </div>
                                <small class="text-muted">الصيغ المدعومة: .xlsx, .xls, .csv</small>

                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary shadow-sm"
                                    style="background: #0078D7; border: none; transition: 0.3s;">
                                    📥 رفع واستيراد
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-2 rounded-bottom-4">
                        <small class="text-muted">تأكد من صحة البيانات قبل رفع الملف</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
