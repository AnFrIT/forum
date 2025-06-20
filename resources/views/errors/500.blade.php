@extends('layouts.app')

@section('title', 'Внутренняя ошибка сервера')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12">
    <div class="max-w-2xl mx-auto text-center px-4">
        <!-- Анимированная иллюстрация -->
        <div class="mb-8 relative">
            <div class="text-9xl font-bold text-red-600 opacity-20 select-none">500</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="animate-pulse text-6xl">💥</div>
            </div>
        </div>

        <!-- Заголовок -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
            Что-то пошло не так
        </h1>
        
        <p class="text-lg text-gray-600 mb-8 max-w-lg mx-auto">
            Произошла внутренняя ошибка сервера. Наши разработчики уже уведомлены о проблеме 
            и работают над её устранением.
        </p>

        <!-- Информация об ошибке -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8 text-left">
            <h3 class="font-semibold text-red-800 mb-3">🔧 Что это означает:</h3>
            <ul class="space-y-2 text-red-700 text-sm">
                <li class="flex items-start">
                    <span class="text-red-500 mr-2 mt-0.5">•</span>
                    <span>Сервер столкнулся с неожиданной проблемой</span>
                </li>
                <li class="flex items-start">
                    <span class="text-red-500 mr-2 mt-0.5">•</span>
                    <span>Ошибка автоматически отправлена разработчикам</span>
                </li>
                <li class="flex items-start">
                    <span class="text-red-500 mr-2 mt-0.5">•</span>
                    <span>Мы работаем над исправлением проблемы</span>
                </li>
                <li class="flex items-start">
                    <span class="text-red-500 mr-2 mt-0.5">•</span>
                    <span>Обычно такие проблемы решаются в течение нескольких минут</span>
                </li>
            </ul>
        </div>

        <!-- Что можно сделать -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 text-left">
            <h3 class="font-semibold text-blue-800 mb-3">💡 Что вы можете сделать:</h3>
            <ol class="space-y-2 text-blue-700 text-sm list-decimal list-inside">
                <li>Подождать несколько минут и попробовать снова</li>
                <li>Обновить страницу (Ctrl+F5 или Cmd+R)</li>
                <li>Очистить кэш браузера</li>
                <li>Попробовать другой браузер</li>
                <li>Обратиться в службу поддержки, если проблема повторяется</li>
            </ol>
        </div>

        <!-- Действия -->
        <div class="space-y-4 mb-8">
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="reloadPage()" 
                        class="px-8 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    🔄 Обновить страницу
                </button>
                
                <a href="{{ route('home') }}" 
                   class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    🏠 На главную страницу
                </a>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="goBack()" 
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    ← Вернуться назад
                </button>
                
                <button onclick="reportError()" 
                        class="px-6 py-2 border border-orange-300 text-orange-700 rounded-lg hover:bg-orange-50 transition-colors">
                    📧 Сообщить об ошибке
                </button>
            </div>
        </div>

        <!-- Статус сервера -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h3 class="font-semibold text-gray-800 mb-4">📊 Статус системы</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- API -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-sm text-gray-700">API</span>
                    <div class="flex items-center">
                        <div id="api-status" class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span id="api-text" class="text-sm text-yellow-600">Проверка...</span>
                    </div>
                </div>
                
                <!-- База данных -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-sm text-gray-700">База данных</span>
                    <div class="flex items-center">
                        <div id="db-status" class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span id="db-text" class="text-sm text-yellow-600">Проверка...</span>
                    </div>
                </div>
                
                <!-- Кэш -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-sm text-gray-700">Кэш</span>
                    <div class="flex items-center">
                        <div id="cache-status" class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span id="cache-text" class="text-sm text-yellow-600">Проверка...</span>
                    </div>
                </div>
                
                <!-- Файловая система -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-sm text-gray-700">Файлы</span>
                    <div class="flex items-center">
                        <div id="storage-status" class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span id="storage-text" class="text-sm text-yellow-600">Проверка...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Альтернативные действия -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h3 class="font-semibold text-gray-800 mb-4">🔗 Популярные разделы</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @if(isset($popularCategories) && $popularCategories->count() > 0)
                    @foreach($popularCategories->take(6) as $category)
                        <a href="{{ route('categories.show', $category) }}" 
                           class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-left">
                            <div class="font-medium text-gray-800">{{ $category->name }}</div>
                            <div class="text-sm text-gray-600">{{ $category->topics_count ?? 0 }} тем</div>
                        </a>
                    @endforeach
                @else
                    <div class="col-span-full p-4 text-center text-gray-500">
                        <p class="mb-2">Популярные разделы временно недоступны</p>
                        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Попробовать главную страницу
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Информация об ошибке для технической поддержки -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8">
            <h3 class="font-semibold text-gray-800 mb-3">🔍 Техническая информация</h3>
            <div class="text-left text-sm text-gray-600 space-y-2">
                <div class="flex justify-between">
                    <span>Код ошибки:</span>
                    <span class="font-mono" id="error-id">{{ $errorId ?? 'ERR-' . date('YmdHis') . '-' . substr(md5(uniqid()), 0, 8) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Время:</span>
                    <span>{{ now()->format('d.m.Y H:i:s') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>URL:</span>
                    <span class="font-mono text-xs break-all">{{ url()->current() }}</span>
                </div>
                @if(request()->userAgent())
                    <div class="flex justify-between">
                        <span>Браузер:</span>
                        <span class="text-xs">{{ Str::limit(request()->userAgent(), 50) }}</span>
                    </div>
                @endif
            </div>
            <p class="text-xs text-gray-500 mt-3">
                Сохраните код ошибки при обращении в службу поддержки
            </p>
        </div>

        <!-- Контакты поддержки -->
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
            <h3 class="font-semibold text-orange-800 mb-3">🆘 Служба поддержки</h3>
            <p class="text-orange-700 text-sm mb-4">
                Если проблема повторяется или вам нужна помощь:
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center text-sm">
                <a href="mailto:support@{{ config('app.domain', 'forum.com') }}?subject=Ошибка 500&body=Код ошибки: {{ $errorId ?? 'ERR-' . date('YmdHis') }}" 
                   class="text-orange-600 hover:text-orange-800 hover:underline">
                    📧 support@{{ config('app.domain', 'forum.com') }}
                </a>
                
                @if(config('app.support_phone'))
                    <a href="tel:{{ config('app.support_phone') }}" 
                       class="text-orange-600 hover:text-orange-800 hover:underline">
                        📞 {{ config('app.support_phone') }}
                    </a>
                @endif
                
                <button onclick="copyErrorInfo()" 
                        class="text-orange-600 hover:text-orange-800 hover:underline">
                    📋 Скопировать информацию об ошибке
                </button>
            </div>
        </div>

        <!-- Автообновление -->
        <div class="mt-8 text-sm text-gray-500">
            <p>Страница будет автоматически обновлена через <span id="countdown">30</span> секунд</p>
            <button onclick="stopCountdown()" class="text-blue-600 hover:text-blue-800 underline ml-2">
                Отменить
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let countdownTimer;
let countdownSeconds = 30;
let countdownStopped = false;

// Обратный отсчет для автообновления
function startCountdown() {
    const countdownElement = document.getElementById('countdown');
    
    countdownTimer = setInterval(() => {
        if (countdownStopped) {
            clearInterval(countdownTimer);
            return;
        }
        
        countdownSeconds--;
        countdownElement.textContent = countdownSeconds;
        
        if (countdownSeconds <= 0) {
            clearInterval(countdownTimer);
            reloadPage();
        }
    }, 1000);
}

function stopCountdown() {
    countdownStopped = true;
    document.querySelector('.mt-8.text-sm.text-gray-500').innerHTML = 
        '<p class="text-gray-500">Автообновление отменено</p>';
}

// Обновление страницы
function reloadPage() {
    window.location.reload();
}

// Возврат назад
function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '{{ route("home") }}';
    }
}

// Сообщить об ошибке
function reportError() {
    const errorId = document.getElementById('error-id').textContent;
    const url = window.location.href;
    const userAgent = navigator.userAgent;
    
    const subject = encodeURIComponent('Ошибка 500 на сайте');
    const body = encodeURIComponent(`
Код ошибки: ${errorId}
URL: ${url}
Время: ${new Date().toLocaleString()}
Браузер: ${userAgent}

Описание проблемы:
[Опишите, что вы делали, когда произошла ошибка]
    `.trim());
    
    window.location.href = `mailto:support@{{ config('app.domain', 'forum.com') }}?subject=${subject}&body=${body}`;
}

// Копировать информацию об ошибке
function copyErrorInfo() {
    const errorId = document.getElementById('error-id').textContent;
    const url = window.location.href;
    const time = new Date().toLocaleString();
    
    const errorInfo = `Код ошибки: ${errorId}\nURL: ${url}\nВремя: ${time}`;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(errorInfo).then(() => {
            alert('Информация об ошибке скопирована в буфер обмена');
        });
    } else {
        // Fallback для старых браузеров
        const textArea = document.createElement('textarea');
        textArea.value = errorInfo;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Информация об ошибке скопирована в буфер обмена');
    }
}

// Проверка статуса системы
async function checkSystemStatus() {
    const checks = [
        { id: 'api', endpoint: '{{ route("api.health.check") }}' },
        { id: 'db', endpoint: '{{ route("api.health.database") }}' },
        { id: 'cache', endpoint: '{{ route("api.health.cache") }}' },
        { id: 'storage', endpoint: '{{ route("api.health.storage") }}' }
    ];
    
    for (const check of checks) {
        try {
            const response = await fetch(check.endpoint, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const statusElement = document.getElementById(`${check.id}-status`);
            const textElement = document.getElementById(`${check.id}-text`);
            
            if (response.ok) {
                statusElement.className = 'w-3 h-3 bg-green-500 rounded-full mr-2';
                textElement.textContent = 'Работает';
                textElement.className = 'text-sm text-green-600';
            } else {
                statusElement.className = 'w-3 h-3 bg-red-500 rounded-full mr-2';
                textElement.textContent = 'Ошибка';
                textElement.className = 'text-sm text-red-600';
            }
        } catch (error) {
            const statusElement = document.getElementById(`${check.id}-status`);
            const textElement = document.getElementById(`${check.id}-text`);
            
            statusElement.className = 'w-3 h-3 bg-red-500 rounded-full mr-2';
            textElement.textContent = 'Недоступен';
            textElement.className = 'text-sm text-red-600';
        }
    }
}

// Автоматическая отправка отчета об ошибке
function sendErrorReport() {
    const errorData = {
        error_id: document.getElementById('error-id').textContent,
        url: window.location.href,
        user_agent: navigator.userAgent,
        timestamp: new Date().toISOString(),
        referrer: document.referrer
    };
    
    fetch('{{ route("api.error-report") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify(errorData)
    }).catch(error => {
        console.log('Error report could not be sent:', error);
    });
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    startCountdown();
    checkSystemStatus();
    sendErrorReport();
    
    // Анимация появления элементов
    const elements = document.querySelectorAll('h1, p, .bg-red-50, .bg-blue-50, .space-y-4');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Повторная попытка через быстрые клавиши
document.addEventListener('keydown', function(e) {
    // F5 или Ctrl+R для обновления
    if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
        e.preventDefault();
        reloadPage();
    }
    
    // ESC для возврата на главную
    if (e.key === 'Escape') {
        window.location.href = '{{ route("home") }}';
    }
});

// Отслеживание ошибок для аналитики
if (typeof gtag !== 'undefined') {
    gtag('event', 'server_error', {
        'error_code': '500',
        'page_url': window.location.href
    });
}
</script>
@endpush

@push('styles')
<style>
/* Анимация пульсации для иконки ошибки */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(1.1);
    }
}

.animate-pulse {
    animation: pulse 2s infinite;
}

/* Градиентный фон для кода ошибки */
.text-9xl {
    background: linear-gradient(135deg, #dc2626, #991b1b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Эффекты при наведении */
.hover\:bg-gray-50:hover {
    background-color: #f9fafb;
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Анимация для статуса индикаторов */
.w-3.h-3 {
    transition: all 0.3s ease;
}

/* Стили для мобильных устройств */
@media (max-width: 640px) {
    .text-9xl {
        font-size: 6rem;
    }
    
    .text-6xl {
        font-size: 3rem;
    }
    
    .text-4xl {
        font-size: 2rem;
    }
}

/* Стили для технической информации */
.font-mono {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

/* Анимация обратного отсчета */
#countdown {
    font-weight: bold;
    color: #dc2626;
    transition: color 0.3s ease;
}

/* Пульсация для кнопки обновления */
button:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

/* Особые стили для критических сообщений */
.bg-red-50 {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
@endpush
@endsection