@extends('layouts.app')

@section('title', 'Страница не найдена')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12">
    <div class="max-w-2xl mx-auto text-center px-4">
        <!-- Анимированная иллюстрация -->
        <div class="mb-8 relative">
            <div class="text-9xl font-bold text-blue-600 opacity-20 select-none">404</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="animate-bounce text-6xl">🔍</div>
            </div>
        </div>

        <!-- Заголовок -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
            Страница не найдена
        </h1>
        
        <p class="text-lg text-gray-600 mb-8 max-w-lg mx-auto">
            К сожалению, страница, которую вы ищете, не существует или была перемещена. 
            Возможно, она была удалена или у вас неправильная ссылка.
        </p>

        <!-- Возможные причины -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 text-left">
            <h3 class="font-semibold text-blue-800 mb-3">🤔 Возможные причины:</h3>
            <ul class="space-y-2 text-blue-700 text-sm">
                <li class="flex items-start">
                    <span class="text-blue-500 mr-2 mt-0.5">•</span>
                    <span>Неправильно введен URL-адрес</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-500 mr-2 mt-0.5">•</span>
                    <span>Страница была удалена или перемещена</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-500 mr-2 mt-0.5">•</span>
                    <span>Ссылка устарела или неактивна</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-500 mr-2 mt-0.5">•</span>
                    <span>У вас нет прав доступа к этой странице</span>
                </li>
            </ul>
        </div>

        <!-- Действия -->
        <div class="space-y-4 mb-8">
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}" 
                   class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    🏠 На главную страницу
                </a>
                
                <button onclick="goBack()" 
                        class="px-8 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    ← Вернуться назад
                </button>
            </div>
            
            <!-- Поиск -->
            <div class="mt-6">
                <form method="GET" action="{{ route('search') }}" class="flex max-w-md mx-auto">
                    <input type="text" name="q" 
                           placeholder="Поиск по форуму..." 
                           class="flex-grow px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" 
                            class="px-6 py-3 bg-gray-600 text-white rounded-r-lg hover:bg-gray-700 transition-colors">
                        🔍
                    </button>
                </form>
                <p class="text-sm text-gray-500 mt-2">Попробуйте найти то, что вы искали</p>
            </div>
        </div>

        <!-- Популярные разделы -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="font-semibold text-gray-800 mb-4">🔥 Популярные разделы</h3>
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
                    <div class="col-span-full text-center text-gray-500">
                        <p>Популярные разделы пока не определены</p>
                        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Посетить главную страницу
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Последние темы -->
        @if(isset($recentTopics) && $recentTopics->count() > 0)
            <div class="bg-white border border-gray-200 rounded-lg p-6 mt-6">
                <h3 class="font-semibold text-gray-800 mb-4">📈 Последние темы</h3>
                <div class="space-y-3">
                    @foreach($recentTopics->take(5) as $topic)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-grow text-left">
                                <a href="{{ route('topics.show', $topic) }}" 
                                   class="font-medium text-blue-600 hover:text-blue-800 line-clamp-1">
                                    {{ $topic->title }}
                                </a>
                                <div class="text-sm text-gray-500">
                                    в <a href="{{ route('categories.show', $topic->category) }}" 
                                         class="text-gray-600 hover:text-gray-800">{{ $topic->category->name }}</a>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500 ml-4">
                                {{ $topic->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Полезные ссылки -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="font-semibold text-gray-800 mb-4">🔗 Полезные ссылки</h3>
            <div class="flex flex-wrap justify-center gap-4 text-sm">
                <a href="{{ route('home') }}" 
                   class="text-blue-600 hover:text-blue-800 hover:underline">
                    Главная страница
                </a>
                
                @if(isset($categories) && $categories->count() > 0)
                    <a href="{{ route('categories.show', $categories->first()) }}" 
                       class="text-blue-600 hover:text-blue-800 hover:underline">
                        Форумы
                    </a>
                @endif
                
                <a href="{{ route('search') }}" 
                   class="text-blue-600 hover:text-blue-800 hover:underline">
                    Поиск
                </a>
                
                @guest
                    <a href="{{ route('login') }}" 
                       class="text-blue-600 hover:text-blue-800 hover:underline">
                        Вход
                    </a>
                    <a href="{{ route('register') }}" 
                       class="text-blue-600 hover:text-blue-800 hover:underline">
                        Регистрация
                    </a>
                @else
                    <a href="{{ route('profile.show', auth()->user()) }}" 
                       class="text-blue-600 hover:text-blue-800 hover:underline">
                        Мой профиль
                    </a>
                    <a href="{{ route('messages.index') }}" 
                       class="text-blue-600 hover:text-blue-800 hover:underline">
                        Сообщения
                    </a>
                @endguest
            </div>
        </div>

        <!-- Контакты -->
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="font-semibold text-yellow-800 mb-3">🆘 Нужна помощь?</h3>
            <p class="text-yellow-700 text-sm mb-4">
                Если вы считаете, что это ошибка, или у вас есть вопросы, свяжитесь с нами:
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center text-sm">
                <a href="mailto:support@{{ config('app.domain', 'forum.com') }}" 
                   class="text-yellow-600 hover:text-yellow-800 hover:underline">
                    📧 Написать в поддержку
                </a>
                <a href="#" onclick="reportBrokenLink()" 
                   class="text-yellow-600 hover:text-yellow-800 hover:underline">
                    🔗 Сообщить о сломанной ссылке
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Возврат на предыдущую страницу
function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '{{ route("home") }}';
    }
}

// Сообщить о сломанной ссылке
function reportBrokenLink() {
    const currentUrl = window.location.href;
    const referrer = document.referrer || 'Неизвестно';
    
    if (confirm('Сообщить о сломанной ссылке? Мы отправим информацию администраторам.')) {
        fetch('{{ route("api.report-broken-link") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({
                url: currentUrl,
                referrer: referrer,
                user_agent: navigator.userAgent
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Спасибо за сообщение! Мы исправим эту проблему.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback - mailto
            window.location.href = `mailto:support@{{ config('app.domain', 'forum.com') }}?subject=Сломанная ссылка&body=URL: ${encodeURIComponent(currentUrl)}%0AПредыдущая страница: ${encodeURIComponent(referrer)}`;
        });
    }
}

// Анимация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    // Анимация появления элементов
    const elements = document.querySelectorAll('h1, p, .bg-blue-50, .space-y-4');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Фокус на поле поиска после небольшой задержки
    setTimeout(() => {
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.focus();
        }
    }, 1000);
});

// Отслеживание 404 ошибок для аналитики
if (typeof gtag !== 'undefined') {
    gtag('event', 'page_not_found', {
        'page_url': window.location.href,
        'referrer': document.referrer
    });
}

// Предложения автодополнения для поиска
const searchInput = document.querySelector('input[name="q"]');
if (searchInput) {
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                // Здесь можно добавить AJAX запрос для автодополнения
                console.log('Searching for:', query);
            }, 300);
        }
    });
}

// Добавление в избранное (если пользователь авторизован)
@auth
function addToFavorites(url, title) {
    fetch('{{ route("api.favorites.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            url: url,
            title: title,
            type: 'broken_link_recovery'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Добавлено в избранное');
        }
    });
}
@endauth
</script>
@endpush

@push('styles')
<style>
/* Анимация для иконки поиска */
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0,-30px,0);
    }
    70% {
        transform: translate3d(0,-15px,0);
    }
    90% {
        transform: translate3d(0,-4px,0);
    }
}

.animate-bounce {
    animation: bounce 2s infinite;
}

/* Ограничение текста в одну строку */
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Стили для плавного появления */
.fade-in {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.fade-in.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Эффект при наведении на карточки */
.bg-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
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

/* Градиентный фон для больших чисел */
.text-9xl {
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endpush
@endsection