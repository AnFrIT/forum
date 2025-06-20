@extends('layouts.app')

@section('title', __('auth.login') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-700">{{ __('auth.sign_in') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('auth.login_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('auth.username') }} *
                </label>
                <input type="text" name="name" id="name" required autofocus
                       value="{{ old('name') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="{{ __('auth.username_placeholder') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('auth.password') }} *
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="{{ __('auth.password_placeholder') }}">
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" 
                           {{ old('remember') ? 'checked' : '' }}
                           class="mr-2 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="text-sm text-gray-700">{{ __('auth.remember_me') }}</span>
                </label>

                <a href="{{ route('password.request') }}" 
                   class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                    {{ __('auth.forgot_password') }}
                </a>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                🔑 {{ __('auth.sign_in') }}
            </button>
        </form>

        <!-- Divider -->
        <div class="my-6 flex items-center">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="mx-4 text-sm text-gray-500">{{ __('auth.or') }}</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <p class="text-gray-600">
                {{ __('auth.not_registered') }}
                <a href="{{ route('register') }}" 
                   class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                    {{ __('auth.sign_up') }}
                </a>
            </p>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-4">
            <a href="{{ route('home') }}" 
               class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                ← {{ __('auth.back_to_home') }}
            </a>
        </div>
    </div>

    <!-- Quick Login Help -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-medium text-blue-800 mb-2">💡 {{ __('auth.quick_login') }}</h3>
        <div class="text-sm text-blue-700 space-y-1">
            <p>• {{ __('auth.quick_login_tip1') }}</p>
            <p>• {{ __('auth.quick_login_tip2') }}</p>
            <p>• {{ __('auth.quick_login_tip3') }}</p>
        </div>
    </div>

    <!-- Security Notice -->
    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h3 class="font-medium text-yellow-800 mb-2">🔒 {{ __('auth.security') }}</h3>
        <div class="text-sm text-yellow-700 space-y-1">
            <p>• {{ __('auth.security_tip1') }}</p>
            <p>• {{ __('auth.security_tip2') }}</p>
            <p>• {{ __('auth.security_tip3') }}</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Обработка Enter в форме
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const form = document.querySelector('form');
        if (form) {
            form.submit();
        }
    }
});

// Автофокус на поле имени пользователя при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    if (nameInput && !nameInput.value) {
        nameInput.focus();
    }
});
</script>
@endpush
@endsection