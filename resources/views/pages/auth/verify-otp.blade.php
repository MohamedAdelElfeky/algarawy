<x-auth-layout>

    <form method="POST" action="{{ route('password.verify_otp') }}">
        @csrf

        <input type="hidden" name="email" value="{{ $otpModel->email }}">
        <div class="form-group">
            <label for="otp">{{ __('OTP') }}</label>
            <input id="otp" type="text" class="form-control @error('otp') is-invalid @enderror" name="otp"
                required autofocus>           
        </div>

        <div class="form-group mb-0">
            <button type="submit" class="btn btn-primary">
                {{ __('Verify OTP') }}
            </button>
        </div>
    </form>
</x-auth-layout>
