@extends('layouts.app')

@section('title', 'Восстановление пароля')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🔑</div>
            <h1 class="text-3xl font-bold text-gray-700">Забыли пароль?</h1>
            <p class="text-gray-600 mt-2">
                Не беспокойтесь! Введите ваш email адрес и мы отправим вам ссылку для восстановления пароля.
            </p>
        </div>

        <!-- Статус отправки -->
        @if(session('status'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                <div class="flex items-center">
                    <span class="text-green-500 mr-2">✅</span>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email адрес *
                </label>
                <div class="relative">
                    <input type="email" name="email" id="email" required autofocus
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-12"
                           placeholder="example@domain.com">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        📧
                    </div>
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <span class="mr-1">⚠️</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                📤 Отправить ссылку для восстановления
            </button>
        </form>

        <!-- Divider -->
        <div class="my-6 flex items-center">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="mx-4 text-sm text-gray-500">или</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <!-- Альтернативные варианты -->
        <div class="space-y-4">
            <div class="text-center">
                <p class="text-gray-600 text-sm mb-4">Вспомнили пароль?</p>
                <a href="{{ route('login') }}" 
                   class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                    Войти в аккаунт
                </a>
            </div>

            <div class="text-center">
                <p class="text-gray-600 text-sm mb-4">Нет аккаунта?</p>
                <a href="{{ route('register') }}" 
                   class="text-green-600 hover:text-green-800 hover:underline font-medium">
                    Создать новый аккаунт
                </a>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" 
               class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                ← Вернуться на главную
            </a>
        </div>
    </div>

    <!-- Инструкции -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="font-medium text-blue-800 mb-3">📋 Инструкции по восстановлению</h3>
        <div class="text-sm text-blue-700 space-y-2">
            <div class="flex items-start">
                <span class="text-blue-500 mr-2 mt-0.5">1️⃣</span>
                <span>Введите email адрес, который вы использовали при регистрации</span>
            </div>
            <div class="flex items-start">
                <span class="text-blue-500 mr-2 mt-0.5">2️⃣</span>
                <span>Проверьте вашу почту (включая папку "Спам")</span>
            </div>
            <div class="flex items-start">
                <span class="text-blue-500 mr-2 mt-0.5">3️⃣</span>
                <span>Перейдите по ссылке в письме для создания нового пароля</span>
            </div>
            <div class="flex items-start">
                <span class="text-blue-500 mr-2 mt-0.5">4️⃣</span>
                <span>Войдите в аккаунт с новым паролем</span>
            </div>
        </div>
    </div>

    <!-- Частые вопросы -->
    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="font-medium text-gray-800 mb-3">❓ Часто задаваемые вопросы</h3>
        <div class="space-y-4 text-sm">
            <div>
                <button class="flex items-center justify-between w-full text-left font-medium text-gray-700 hover:text-blue-600" 
                        onclick="toggleFaq('faq1')">
                    <span>Не приходит письмо с восстановлением</span>
                    <span class="transform transition-transform" id="faq1-icon">▼</span>
                </button>
                <div id="faq1" class="hidden mt-2 text-gray-600">
                    <p>Проверьте папку "Спам" или "Нежелательная почта". Письмо может прийти в течение 5-10 минут. Убедитесь, что вы ввели правильный email адрес.</p>
                </div>
            </div>

            <div>
                <button class="flex items-center justify-between w-full text-left font-medium text-gray-700 hover:text-blue-600" 
                        onclick="toggleFaq('faq2')">
                    <span>Ссылка в письме не работает</span>
                    <span class="transform transition-transform" id="faq2-icon">▼</span>
                </button>
                <div id="faq2" class="hidden mt-2 text-gray-600">
                    <p>Ссылки для восстановления действительны только 60 минут. Если время истекло, запросите новую ссылку. Также убедитесь, что вы копируете ссылку полностью.</p>
                </div>
            </div>

            <div>
                <button class="flex items-center justify-between w-full text-left font-medium text-gray-700 hover:text-blue-600" 
                        onclick="toggleFaq('faq3')">
                    <span>Не помню email для регистрации</span>
                    <span class="transform transition-transform" id="faq3-icon">▼</span>
                </button>
                <div id="faq3" class="hidden mt-2 text-gray-600">
                    <p>Попробуйте все email адреса, которые вы могли использовать. Если не помогает, свяжитесь с администрацией форума через контактную форму.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Контакты поддержки -->
    <div class="mt-6 bg-orange-50 border border-orange-200 rounded-lg p-6">
        <h3 class="font-medium text-orange-800 mb-3">🆘 Нужна помощь?</h3>
        <div class="text-sm text-orange-700 space-y-2">
            <p>Если у вас все еще возникают проблемы с восстановлением пароля, свяжитесь с нами:</p>
            <div class="flex flex-col space-y-1">
                <a href="mailto:support@forum.com" class="text-orange-600 hover:underline">
                    📧 support@forum.com
                </a>
                <a href="#" class="text-orange-600 hover:underline">
                    💬 Форма обратной связи
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Переключение FAQ
function toggleFaq(faqId) {
    const content = document.getElementById(faqId);
    const icon = document.getElementById(faqId + '-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Автофокус на поле email при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    if (emailInput && !emailInput.value) {
        emailInput.focus();
    }
});

// Валидация email в реальном времени
document.getElementById('email').addEventListener('input', function() {
    const email = this.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        this.classList.add('border-red-500');
        this.classList.remove('border-gray-300');
        
        // Показываем подсказку
        let hint = this.parentNode.querySelector('.email-hint');
        if (!hint) {
            hint = document.createElement('p');
            hint.className = 'email-hint mt-1 text-sm text-red-600';
            hint.innerHTML = '⚠️ Пожалуйста, введите корректный email адрес';
            this.parentNode.appendChild(hint);
        }
    } else {
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300');
        
        // Убираем подсказку
        const hint = this.parentNode.querySelector('.email-hint');
        if (hint) {
            hint.remove();
        }
    }
});

// Обработка отправки формы
document.querySelector('form').addEventListener('submit', function(e) {
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Показываем индикатор загрузки
    submitButton.disabled = true;
    submitButton.innerHTML = '⏳ Отправка...';
    
    // Если форма не прошла валидацию, восстанавливаем кнопку
    setTimeout(() => {
        if (submitButton.disabled) {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }, 5000);
});

// Автоматическое закрытие уведомлений через 10 секунд
document.addEventListener('DOMContentLoaded', function() {
    const statusAlert = document.querySelector('.bg-green-100');
    if (statusAlert) {
        setTimeout(() => {
            statusAlert.style.transition = 'opacity 0.5s ease-out';
            statusAlert.style.opacity = '0';
            setTimeout(() => {
                statusAlert.remove();
            }, 500);
        }, 10000);
    }
});

// Проверка доступности email (опционально)
function checkEmailAvailability(email) {
    if (!email) return;
    
    fetch('{{ route("api.check-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        const emailInput = document.getElementById('email');
        if (!data.exists) {
            // Email не найден в базе
            let hint = emailInput.parentNode.querySelector('.email-not-found');
            if (!hint) {
                hint = document.createElement('p');
                hint.className = 'email-not-found mt-1 text-sm text-orange-600';
                hint.innerHTML = '⚠️ Этот email не найден в нашей базе. Проверьте правильность написания.';
                emailInput.parentNode.appendChild(hint);
            }
        } else {
            // Убираем предупреждение если email найден
            const hint = emailInput.parentNode.querySelector('.email-not-found');
            if (hint) {
                hint.remove();
            }
        }
    })
    .catch(error => {
        console.error('Error checking email:', error);
    });
}

// Проверяем email с задержкой при вводе
let emailCheckTimeout;
document.getElementById('email').addEventListener('input', function() {
    clearTimeout(emailCheckTimeout);
    emailCheckTimeout = setTimeout(() => {
        const email = this.value.trim();
        if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            checkEmailAvailability(email);
        }
    }, 1000);
});
</script>
@endpush

@push('styles')
<style>
.transform {
    transition: transform 0.2s ease;
}

.bg-green-100 {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Стили для валидации */
.border-red-500:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
}
</style>
@endpush
@endsection