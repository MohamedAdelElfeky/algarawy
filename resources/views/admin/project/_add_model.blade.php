<div class="modal fade" id="kt_modal_family" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog mw-650px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    X
                </button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                <form id="familyForm" enctype="multipart/form-data">
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">Description</label>
                        <input type="text" class="form-control form-control-solid" placeholder="Enter Description" name="description" required />
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">Location</label>
                        <input type="text" class="form-control form-control-solid" placeholder="Enter Location" name="location" required />
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">Upload Images</label>
                        <input type="file" class="form-control form-control-solid" name="images[]" multiple required />
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">Upload PDF</label>
                        <input type="file" class="form-control form-control-solid" name="files_pdf[]" multiple required />
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal - Invite Friends -->

@section('script')
    <script>
        function addFamily() {
            // Get the form data
            const formData = new FormData(document.getElementById('familyForm'));

            // Send the AJAX request
            fetch('save-family', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    // Handle the response data here (e.g., show success message)
                    console.log(data);
                })
                .catch(error => {
                    // Handle errors here (e.g., show error message)
                    console.error(error);
                });
        }

        // Optional: Trigger the AJAX request when the form is submitted
        document.getElementById('familyForm').addEventListener('submit', function(event) {
            event.preventDefault();
            addFamily();
        });
    </script>
@endsection
