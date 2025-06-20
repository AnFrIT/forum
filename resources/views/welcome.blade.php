@extends('layouts.app')

@section('title', __('main.welcome'))

@section('content')
<!-- Hero секция -->
<div class="bg-gradient-to-br from-blue-600 via-blue-700 to-purple-800 text-white">
    <div class="container mx-auto px-4 py-16 md:py-24">
        <div class="text-center max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                {{ __('main.welcome_message_full') }}
                <span class="bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                    {{ config('app.name') }}
                </span>
            </h1>
            
            <p class="text-xl md:text-2xl mb-8 text-blue-100 leading-relaxed">
                {{ __('main.welcome_description') }}
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}" 
                   class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    🚀 {{ __('main.start_discussion') }}
                </a>
                <a href="{{ route('login') }}" 
                   class="px-8 py-4 border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all duration-300">
                    👋 {{ __('main.i_have_account') }}
                </a>
            </div>
            
            <div class="mt-12 text-blue-200 text-sm">
                <p>🌟 {{ __('main.users_discussing_topics', ['users' => $totalUsers ?? 0, 'topics' => $totalTopics ?? 0]) }}</p>
            </div>
        </div>
    </div>
    
    <!-- Волновой эффект -->
    <div class="relative">
        <svg class="w-full h-12 md:h-20" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,0 C150,120 350,0 600,60 C850,120 1050,0 1200,60 L1200,120 L0,120 Z" 
                  fill="#f0f2f5"></path>
        </svg>
    </div>
</div>

<!-- Особенности -->
<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                {{ __('main.why_choose_our_forum') }}
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                {{ __('main.platform_description') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Особенность 1 -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="text-5xl mb-4">💬</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ __('main.feature_live_communication') }}</h3>
                <p class="text-gray-600">
                    {{ __('main.feature_live_communication_description') }}
                </p>
            </div>
            
            <!-- Особенность 2 -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="text-5xl mb-4">🌍</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ __('main.feature_multilingual') }}</h3>
                <p class="text-gray-600">
                    {{ __('main.feature_multilingual_description') }}
                </p>
            </div>
            
            <!-- Особенность 3 -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="text-5xl mb-4">🔒</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ __('main.feature_security') }}</h3>
                <p class="text-gray-600">
                    {{ __('main.feature_security_description') }}
                </p>
            </div>
            
            <!-- Особенность 4 -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="text-5xl mb-4">📱</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ __('main.feature_convenience') }}</h3>
                <p class="text-gray-600">
                    {{ __('main.feature_convenience_description') }}
                </p>
            </div>
            
            <!-- Особенность 5 -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="text-5xl mb-4">⚡</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ __('main.feature_speed') }}</h3>
                <p class="text-gray-600">
                    {{ __('main.feature_speed_description') }}
                </p>
            </div>
            
            <!-- Особенность 6 -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="text-5xl mb-4">🎯</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ __('main.feature_organization') }}</h3>
                <p class="text-gray-600">
                    {{ __('main.feature_organization_description') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Категории -->
@if(isset($categories) && $categories->count() > 0)
<div class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                {{ __('main.popular_categories') }}
            </h2>
            <p class="text-lg text-gray-600">
                {{ __('main.choose_topic_start') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories->take(6) as $category)
                <a href="{{ route('categories.show', $category) }}" 
                   class="group block p-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-300">
                    
                    <div class="flex items-center mb-4">
                        <div class="text-3xl mr-4">{{ $category->icon ?? '📁' }}</div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                                {{ $category->name }}
                            </h3>
                            @if($category->name_ar)
                                <p class="text-sm text-gray-500 arabic-text">{{ $category->name_ar }}</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($category->description)
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ $category->description }}
                        </p>
                    @endif
                    
                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                        <span>📝 {{ $category->topics_count ?? 0 }} {{ __('main.topics_count') }}</span>
                        <span>💬 {{ $category->posts_count ?? 0 }} {{ __('main.posts_count') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        
        <div class="text-center mt-8">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                {{ __('main.view_all_categories') }} →
            </a>
        </div>
    </div>
</div>
@endif

<!-- Последние обсуждения -->
@if(isset($recentTopics) && $recentTopics->count() > 0)
<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                {{ __('main.recent_discussions') }}
            </h2>
            <p class="text-lg text-gray-600">
                {{ __('main.see_current_topics') }}
            </p>
        </div>
        
        <div class="max-w-4xl mx-auto space-y-4">
            @foreach($recentTopics->take(5) as $topic)
                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                <a href="{{ route('topics.show', $topic) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    @if($topic->is_pinned) 📌 @endif
                                    {{ $topic->title }}
                                </a>
                            </h3>
                            
                            <div class="flex items-center text-sm text-gray-600 space-x-4 mb-3">
                                <span>👤 {{ $topic->user->name }}</span>
                                <span>📁 {{ $topic->category->name }}</span>
                                <span>🕒 {{ $topic->created_at->diffForHumans() }}</span>
                            </div>
                            
                            @if($topic->description || $topic->posts->first())
                                <p class="text-gray-700 text-sm line-clamp-2">
                                    {{ Str::limit(strip_tags($topic->description ?? $topic->posts->first()->content ?? ''), 150) }}
                                </p>
                            @endif
                        </div>
                        
                        <div class="ml-6 text-right text-sm text-gray-500">
                            <div class="font-medium">{{ $topic->posts_count ?? 0 }}</div>
                            <div class="text-xs">{{ __('main.replies') }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-8">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                {{ __('main.view_all_discussions') }} →
            </a>
        </div>
    </div>
</div>
@endif

<!-- Призыв к действию -->
<div class="py-16 bg-gradient-to-r from-blue-600 to-purple-700 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            {{ __('main.ready_to_join') }}
        </h2>
        
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            {{ __('main.registration_takes_minute') }}
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('register') }}" 
               class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                🎯 {{ __('main.register_free') }}
            </a>
            <a href="{{ route('home') }}" 
               class="px-8 py-4 border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all duration-300">
                👀 {{ __('main.look_first') }}
            </a>
        </div>
        
        <div class="mt-8 text-blue-200 text-sm">
            <p>✨ {{ __('main.registration_free_and_fast') }}</p>
        </div>
    </div>
</div>

<!-- Статистика -->
<div class="py-12 bg-white border-t border-gray-200">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-3xl font-bold text-blue-600">{{ $totalUsers ?? 0 }}</div>
                <div class="text-gray-600">{{ __('main.users') }}</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-green-600">{{ $totalTopics ?? 0 }}</div>
                <div class="text-gray-600">{{ __('main.topics') }}</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-purple-600">{{ $totalPosts ?? 0 }}</div>
                <div class="text-gray-600">{{ __('main.posts') }}</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-orange-600">{{ $onlineUsers ?? 0 }}</div>
                <div class="text-gray-600">{{ __('main.online') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ -->
<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                {{ __('main.faq') }}
            </h2>
            <p class="text-lg text-gray-600">
                {{ __('main.faq_description') }}
            </p>
        </div>
        
        <div class="max-w-3xl mx-auto space-y-4">
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full p-6 text-left font-semibold text-gray-800 hover:text-blue-600 transition-colors" 
                        onclick="toggleFaq('faq1')">
                    <div class="flex items-center justify-between">
                        <span>{{ __('main.faq_registration') }}</span>
                        <span id="faq1-icon" class="transform transition-transform">▼</span>
                    </div>
                </button>
                <div id="faq1" class="hidden p-6 pt-0 text-gray-600">
                    <p>{{ __('main.faq_registration_answer') }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full p-6 text-left font-semibold text-gray-800 hover:text-blue-600 transition-colors" 
                        onclick="toggleFaq('faq2')">
                    <div class="flex items-center justify-between">
                        <span>{{ __('main.faq_languages') }}</span>
                        <span id="faq2-icon" class="transform transition-transform">▼</span>
                    </div>
                </button>
                <div id="faq2" class="hidden p-6 pt-0 text-gray-600">
                    <p>{{ __('main.faq_languages_answer') }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full p-6 text-left font-semibold text-gray-800 hover:text-blue-600 transition-colors" 
                        onclick="toggleFaq('faq3')">
                    <div class="flex items-center justify-between">
                        <span>{{ __('main.faq_moderation') }}</span>
                        <span id="faq3-icon" class="transform transition-transform">▼</span>
                    </div>
                </button>
                <div id="faq3" class="hidden p-6 pt-0 text-gray-600">
                    <p>{{ __('main.faq_moderation_answer') }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full p-6 text-left font-semibold text-gray-800 hover:text-blue-600 transition-colors" 
                        onclick="toggleFaq('faq4')">
                    <div class="flex items-center justify-between">
                        <span>{{ __('main.faq_create_topic') }}</span>
                        <span id="faq4-icon" class="transform transition-transform">▼</span>
                    </div>
                </button>
                <div id="faq4" class="hidden p-6 pt-0 text-gray-600">
                    <p>{{ __('main.faq_create_topic_answer') }}</p>
                </div>
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

// Анимация при прокрутке
function animateOnScroll() {
    const elements = document.querySelectorAll('.transform');
    
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('animate-fadeInUp');
        }
    });
}

// Плавная прокрутка к якорям
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Счетчики с анимацией
function animateCounters() {
    const counters = document.querySelectorAll('.text-3xl.font-bold');
    
    counters.forEach(counter => {
        const target = parseInt(counter.textContent);
        const increment = target / 100;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            counter.textContent = Math.floor(current);
            
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            }
        }, 20);
    });
}

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    // Анимация элементов при загрузке
    const heroElements = document.querySelectorAll('.text-4xl, .text-xl, .flex');
    heroElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            element.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 200);
    });
    
    // Запуск анимации счетчиков при появлении в области видимости
    const statsSection = document.querySelector('.grid.grid-cols-2.md\\:grid-cols-4');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(statsSection);
    }
});

// Обработчик прокрутки
window.addEventListener('scroll', animateOnScroll);

// Эффект параллакса для hero секции
window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector('.bg-gradient-to-br');
    
    if (hero) {
        hero.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});

// Модальное окно для регистрации (если нужно)
function showRegistrationModal() {
    // Здесь можно добавить модальное окно регистрации
    console.log('Opening registration modal');
}
</script>
@endpush

@push('styles')
<style>
/* Градиентный текст */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}

/* Ограничение текста */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Анимации */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.8s ease-out;
}

/* Эффекты при наведении */
.group:hover .group-hover\:text-blue-600 {
    color: #2563eb;
    transition: color 0.3s ease;
}

/* Стили для арабского текста */
.arabic-text {
    font-family: 'Amiri', serif;
    direction: rtl;
    text-align: right;
}

/* Плавная прокрутка */
html {
    scroll-behavior: smooth;
}

/* Эффект волны */
.wave {
    animation: wave 3s ease-in-out infinite;
}

@keyframes wave {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Адаптивность для мобильных устройств */
@media (max-width: 640px) {
    .text-4xl.md\:text-6xl {
        font-size: 2.5rem;
        line-height: 1.2;
    }
    
    .text-xl.md\:text-2xl {
        font-size: 1.1rem;
    }
    
    .py-16.md\:py-24 {
        padding-top: 3rem;
        padding-bottom: 3rem;
    }
}

/* Кастомные стили для кнопок */
.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1e40af, #1e3a8a);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
}

/* Стили для карточек особенностей */
.feature-card {
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Анимация появления элементов */
.fade-in {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease, transform 0.8s ease;
}

.fade-in.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Стили для статистики */
.stat-number {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endpush
@endsection