@extends('layouts.app')

@section('title', __('auth.register') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-700">{{ __('auth.register') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('auth.register_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('auth.username') }} *
                </label>
                <input type="text" name="name" id="name" required autofocus
                       value="{{ old('name') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="{{ __('auth.username_placeholder') }}">
                <p class="mt-1 text-xs text-gray-500">{{ __('auth.username_help') }}</p>
                <p class="mt-1 text-xs text-yellow-600 font-semibold">{{ __('auth.username_requirements') }}</p>
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
                           placeholder="{{ __('auth.password_min_length') }}">
                </div>
                
                <!-- Password strength indicator -->
                <div class="mt-2" id="password-strength-container" style="display: none;">
                    <div class="flex space-x-1">
                        <div id="strength-1" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                        <div id="strength-2" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                        <div id="strength-3" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                        <div id="strength-4" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                    </div>
                    <p id="strength-text" class="text-xs text-gray-500 mt-1">{{ __('auth.enter_password') }}</p>
                </div>
                
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('auth.confirm_password') }} *
                </label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="{{ __('auth.repeat_password') }}">
                </div>
                <div id="password-match" class="mt-1 text-xs"></div>
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>



            <!-- Terms and Privacy -->
            <div class="mb-6">
                <label class="flex items-start">
                    <input type="checkbox" name="terms" id="terms" required
                           {{ old('terms') ? 'checked' : '' }}
                           class="mr-3 mt-1 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="text-sm text-gray-700">
                        {{ __('auth.i_agree_to') }} 
                        <a href="{{ route('terms') }}" target="_blank" class="text-blue-600 hover:underline">{{ __('auth.terms_and_conditions') }}</a> 
                        {{ __('auth.and') }} 
                        <a href="{{ route('privacy') }}" target="_blank" class="text-blue-600 hover:underline">{{ __('auth.privacy_policy') }}</a>
                    </span>
                </label>
                @error('terms')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submit-btn" 
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                🚀 {{ __('auth.create_account') }}
            </button>
        </form>

        <!-- Divider -->
        <div class="my-6 flex items-center">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="mx-4 text-sm text-gray-500">{{ __('auth.or') }}</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-gray-600">
                {{ __('auth.already_registered') }}
                <a href="{{ route('login') }}" 
                   class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                    {{ __('auth.login') }}
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

    <!-- Registration Guidelines -->
    <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="font-medium text-green-800 mb-2">✅ {{ __('auth.registration_tips') }}</h3>
        <div class="text-sm text-green-700 space-y-1">
            <p>• {{ __('auth.registration_tip1') }}</p>
            <p>• {{ __('auth.registration_tip2') }}</p>
            <p>• {{ __('auth.registration_tip3') }}</p>
        </div>
    </div>

    <!-- Community Rules -->
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-medium text-blue-800 mb-2">📋 {{ __('main.community_rules') }}</h3>
        <div class="text-sm text-blue-700 space-y-1">
            <p>• {{ __('main.community_rule1') }}</p>
            <p>• {{ __('main.community_rule2') }}</p>
            <p>• {{ __('main.community_rule3') }}</p>
            <p>• {{ __('main.community_rule4') }}</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Проверка силы пароля
function checkPasswordStrength(password) {
    let strength = 0;
    const checks = [
        /.{8,}/, // Минимум 8 символов
        /[a-z]/, // Строчные буквы
        /[A-Z]/, // Заглавные буквы
        /[0-9]/, // Цифры
        /[^A-Za-z0-9]/ // Спецсимволы
    ];
    
    checks.forEach(check => {
        if (check.test(password)) strength++;
    });
    
    return Math.min(strength, 4);
}

// Обновление индикатора силы пароля
function updatePasswordStrength() {
    const password = document.getElementById('password').value;
    const container = document.getElementById('password-strength-container');
    const strengthText = document.getElementById('strength-text');
    
    if (password.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    const strength = checkPasswordStrength(password);
    
    // Сброс индикаторов
    for (let i = 1; i <= 4; i++) {
        const indicator = document.getElementById(`strength-${i}`);
        indicator.className = 'h-1 w-1/4 bg-gray-200 rounded';
    }
    
    // Установка цвета
    const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
    const texts = ['Очень слабый', 'Слабый', 'Средний', 'Сильный'];
    const textColors = ['text-red-600', 'text-orange-600', 'text-yellow-600', 'text-green-600'];
    
    for (let i = 1; i <= strength; i++) {
        const indicator = document.getElementById(`strength-${i}`);
        indicator.className = `h-1 w-1/4 ${colors[strength - 1]} rounded`;
    }
    
    if (strength > 0) {
        strengthText.textContent = texts[strength - 1];
        strengthText.className = `text-xs mt-1 ${textColors[strength - 1]}`;
    }
}

// Проверка совпадения паролей
function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const matchDiv = document.getElementById('password-match');
    
    if (confirmation.length === 0) {
        matchDiv.textContent = '';
        return false;
    }
    
    if (password === confirmation) {
        matchDiv.textContent = '✅ Пароли совпадают';
        matchDiv.className = 'mt-1 text-xs text-green-600';
        return true;
    } else {
        matchDiv.textContent = '❌ Пароли не совпадают';
        matchDiv.className = 'mt-1 text-xs text-red-600';
        return false;
    }
}

// Валидация формы
function validateForm() {
    const name = document.getElementById('name').value.trim();
    const password = document.getElementById('password').value;
    const terms = document.getElementById('terms').checked;
    const passwordMatch = checkPasswordMatch();
    const passwordStrong = checkPasswordStrength(password) >= 2;
    
    const isValid = name.length >= 2 && 
                   password.length >= 8 && 
                   passwordMatch && 
                   passwordStrong && 
                   terms;
    
    const submitBtn = document.getElementById('submit-btn');
    if (isValid) {
        submitBtn.className = 'w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium';
    } else {
        submitBtn.className = 'w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium';
    }
}

// Обработчики событий
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const name = document.getElementById('name');
    const terms = document.getElementById('terms');
    
    // События для полей
    password.addEventListener('input', function() {
        updatePasswordStrength();
        checkPasswordMatch();
        validateForm();
    });
    
    passwordConfirmation.addEventListener('input', function() {
        checkPasswordMatch();
        validateForm();
    });
    
    [name, terms].forEach(field => {
        field.addEventListener('input', validateForm);
        field.addEventListener('change', validateForm);
    });
    
    // Начальная валидация
    validateForm();
});
</script>
@endpush
@endsection