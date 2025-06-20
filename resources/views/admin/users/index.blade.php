@extends('layouts.app')

@section('title', __('main.user_management') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-700">👥 {{ __('main.user_management') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('main.total_users') }}: {{ $users->total() }}</p>
            </div>
            
            <div class="mt-4 lg:mt-0 flex flex-wrap gap-2">
                <button onclick="exportUsers()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    📊 {{ __('main.export') }}
                </button>
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    ← {{ __('main.back') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                <!-- Фильтры -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 flex-grow">
                    <!-- Поиск -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                        <input type="text" name="search" id="search"
                               value="{{ request('search') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md"
                               placeholder="Поиск по имени или email">
                    </div>

                    <!-- Роль -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Роль</label>
                        <select name="role" id="role" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                            <option value="">Все роли</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Администраторы</option>
                            <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>Модераторы</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Пользователи</option>
                        </select>
                    </div>

                    <!-- Статус -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                        <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                            <option value="">Все статусы</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Активные</option>
                            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Заблокированные</option>
                        </select>
                    </div>

                    <!-- Сортировка -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Сортировка</label>
                        <select name="sort" id="sort" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                            <option value="registered_at" {{ request('sort') === 'registered_at' ? 'selected' : '' }}>По дате регистрации</option>
                            <option value="last_activity" {{ request('sort') === 'last_activity' ? 'selected' : '' }}>По последней активности</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>По имени</option>
                        </select>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Применить
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        Сбросить
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Таблица пользователей -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select-all" 
                                   class="text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('main.user') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('main.statistics') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('main.status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('main.registration') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('main.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" 
                                       class="user-checkbox text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </td>
                            
                            <!-- Пользователь -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                                 class="w-8 h-8 rounded-full object-cover mr-3">
                                        @else
                                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <a href="{{ route('profile.show', $user) }}" 
                                               class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                                {{ $user->name }}
                                            </a>
                                            @if($user->role === 'admin')
                                                <span class="ml-2 text-red-500" title="{{ __('main.admin') }}">👑</span>
                                            @elseif($user->role === 'moderator')
                                                <span class="ml-2 text-orange-500" title="{{ __('main.moderator') }}">🛡️</span>
                                            @endif
                                            
                                            @if($user->isOnline())
                                                <span class="ml-2 w-2 h-2 bg-green-500 rounded-full" title="{{ __('main.online') }}"></span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                                        @if($user->country)
                                    <div class="text-xs text-gray-400">🌎 {{ $user->country }}</div>
                                @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Статистика -->
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="text-gray-900">💬 {{ $user->posts_count ?? $user->posts->count() }} {{ __('main.posts') }}</div>
                                    <div class="text-gray-500">📝 {{ $user->topics_count ?? $user->topics->count() }} {{ __('main.topics') }}</div>
                                    @if($user->last_activity_at)
                                        <div class="text-xs text-gray-400">
                                            {{ __('main.activity') }}: {{ $user->last_activity_at->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Статус -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if($user->banned_until)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            🚫 {{ __('main.banned') }}
                                        </span>
                                        <div class="text-xs text-red-600">
                                            {{ __('main.until') }}: {{ $user->banned_until->format('d.m.Y') }}
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✅ {{ __('main.active') }}
                                        </span>
                                    @endif

                                    <div class="text-xs">
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            @if($user->is_admin ?? $user->role === 'admin') bg-red-100 text-red-800
                                            @elseif($user->is_moderator ?? $user->role === 'moderator') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            @if($user->is_admin ?? $user->role === 'admin')
                                                👑 Администратор
                                            @elseif($user->is_moderator ?? $user->role === 'moderator')
                                                🛡️ Модератор
                                            @else
                                                👤 Пользователь
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Регистрация -->
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div>{{ $user->created_at->format('d.m.Y') }}</div>
                                <div class="text-xs">{{ $user->created_at->diffForHumans() }}</div>
                            </td>

                            <!-- Действия -->
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex items-center space-x-1">
                                    <!-- Редактирование -->
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="text-gray-600 hover:text-gray-900 p-1" title="{{ __('main.edit') }}">
                                        ✏️
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <div class="text-4xl mb-2">👥</div>
                                    <p>{{ __('main.no_users_found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Пагинация -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Автоматическая отправка формы при изменении фильтров
document.querySelectorAll('#search, #role, #status, #sort')?.forEach(element => {
    element.addEventListener('change', function() {
        this.closest('form').submit();
    });
});

// Функция показа уведомлений
function showAlert(message, type = 'info') {
    // Создаем контейнер для уведомлений, если его нет
    let container = document.getElementById('alert-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'alert-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }
    
    const alert = document.createElement('div');
    const id = 'alert-' + Date.now();
    
    const colors = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500'
    };
    
    const icons = {
        'success': '✅',
        'error': '❌',
        'warning': '⚠️',
        'info': 'ℹ️'
    };
    
    alert.id = id;
    alert.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out translate-x-full opacity-0`;
    alert.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="mr-2">${icons[type]}</span>
                <p class="text-sm">${message}</p>
            </div>
            <button onclick="this.closest('div[id]').remove()" class="ml-4 text-white hover:text-gray-200">
                ✕
            </button>
        </div>
    `;
    
    container.appendChild(alert);
    
    // Анимация появления
    requestAnimationFrame(() => {
        alert.style.transform = 'translateX(0)';
        alert.style.opacity = '1';
    });
    
    // Автоматическое скрытие через 5 секунд
    setTimeout(() => {
        if (alert && alert.parentNode) {
            alert.style.transform = 'translateX(400px)';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
}
</script>
@endpush
@endsection