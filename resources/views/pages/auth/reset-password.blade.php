<x-auth-layout>
    {{-- <x-auth-card> --}}
    {{-- <x-slot name="logo">
            <a href="/">
                <img src="{{ asset('path-to-your-logo.png') }}" alt="Logo" class="w-20 h-20">
            </a>
        </x-slot> --}}

    <form method="POST" action="{{ route('password.update') }}">
        {{-- <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="{{ route('login') }}" action="password.update"> --}}
            @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Email Address -->
        <div class="mt-4">
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}"
                placeholder="البريد الإلكتروني" autocomplete="off"
                class="form-control bg-transparent w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required autofocus>

            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <input type="password" id="password" name="password" placeholder="كلمة المرور" autocomplete="off"
                class="form-control bg-transparent w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>

            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">           
            <input type="password" id="password_confirmation" name="password_confirmation"
                placeholder="تأكيد كلمة المرور" autocomplete="off"
                class="form-control bg-transparent w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>

            @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-4">      
            <div class="d-grid mb-10">
                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                    @include('partials/general/_button-indicator', ['label' => 'Reset Password'])
                </button>
            </div>
        </div>
    </form>
    {{-- </x-auth-card> --}}
</x-auth-layout>
