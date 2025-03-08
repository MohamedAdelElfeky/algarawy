<div class="modal fade" id="kt_modal_password" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{ __('lang.change_password') }} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="change-password-form">
                    @csrf
                    <input type="hidden" id="user-id" name="user_id">
                    <div class="mb-3">
                        <label class="form-label"> {{ __('lang.new_password') }} </label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"> {{ __('lang.confirm_password') }} </label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('lang.save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
