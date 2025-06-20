@extends('layouts.app')

@section('title', __('main.admin_panel') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg shadow-lg p-6 mb-6 mt-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">👑 {{ __('main.admin_panel') }}</h1>
                <p class="mt-2 opacity-90">{{ __('main.welcome_back') }}, {{ auth()->user()->name }}!</p>
            </div>
            <div class="text-right">
                <div class="text-sm opacity-90">{{ __('main.last_login') }}</div>
                <div class="font-medium">{{ auth()->user()->last_activity_at ? auth()->user()->last_activity_at->format('d.m.Y H:i') : __('main.unknown') }}</div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('admin.categories.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition-colors">
            <div class="text-2xl mb-2">📁</div>
            <div class="font-medium">{{ __('main.new_category') }}</div>
        </a>
        
        <a href="{{ route('admin.users.index') }}" 
           class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition-colors">
            <div class="text-2xl mb-2">👥</div>
            <div class="font-medium">{{ __('main.users') }}</div>
        </a>
        
        <a href="{{ route('admin.reports.index') }}" 
           class="bg-orange-600 hover:bg-orange-700 text-white p-4 rounded-lg text-center transition-colors">
            <div class="text-2xl mb-2">⚠️</div>
            <div class="font-medium">{{ __('main.reports') }}</div>
            @if(isset($pendingReports) && $pendingReports > 0)
                <div class="bg-red-500 text-white text-xs rounded-full px-2 py-1 mt-1">{{ $pendingReports }}</div>
            @endif
        </a>
        
        <a href="{{ route('admin.settings.index') }}" 
           class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition-colors">
            <div class="text-2xl mb-2">⚙️</div>
            <div class="font-medium">{{ __('main.settings') }}</div>
        </a>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-full">
                    <div class="text-blue-600 text-xl">👥</div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">{{ __('main.total_users') }}</div>
                    @if(isset($stats['new_users_today']))
                        <div class="text-xs text-green-600">+{{ $stats['new_users_today'] }} {{ __('main.today') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-full">
                    <div class="text-green-600 text-xl">📝</div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_topics'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">{{ __('main.total_topics') }}</div>
                    @if(isset($stats['new_topics_today']))
                        <div class="text-xs text-green-600">+{{ $stats['new_topics_today'] }} {{ __('main.today') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-full">
                    <div class="text-purple-600 text-xl">💬</div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_posts'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">{{ __('main.total_posts') }}</div>
                    @if(isset($stats['new_posts_today']))
                        <div class="text-xs text-green-600">+{{ $stats['new_posts_today'] }} {{ __('main.today') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-full">
                    <div class="text-orange-600 text-xl">👁️</div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['online_users'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">{{ __('main.online_now') }}</div>
                    @if(isset($stats['peak_online']))
                        <div class="text-xs text-blue-600">{{ __('main.peak') }}: {{ $stats['peak_online'] }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Недавняя активность -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">📊 {{ __('main.recent_activity') }}</h3>
            </div>
            <div class="p-6">
                @if(isset($recentActivity) && $recentActivity->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentActivity as $activity)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        @if($activity->type === 'user_registered')
                                            <span class="text-blue-600 text-sm">👤</span>
                                        @elseif($activity->type === 'topic_created')
                                            <span class="text-green-600 text-sm">📝</span>
                                        @elseif($activity->type === 'post_created')
                                            <span class="text-purple-600 text-sm">💬</span>
                                        @else
                                            <span class="text-gray-600 text-sm">📋</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($activity->type === 'user_registered')
                                                {{ __('main.new_user') }}: {{ optional($activity->user)->name ?? __('main.deleted_user') }}
                                            @elseif($activity->type === 'topic_created')
                                                {{ __('main.new_topic') }}: {{ Str::limit($activity->data['title'] ?? __('main.untitled'), 30) }}
                                            @elseif($activity->type === 'post_created')
                                                {{ __('main.new_post_from') }} {{ optional($activity->user)->name ?? __('main.deleted_user') }}
                                            @else
                                                {{ $activity->description }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        <div class="text-4xl mb-2">📊</div>
                        <p>{{ __('main.no_recent_activity') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Системная информация -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">🖥️ {{ __('main.system_info') }}</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ __('main.laravel_version') }}:</span>
                        <span class="text-sm font-medium">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ __('main.php_version') }}:</span>
                        <span class="text-sm font-medium">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ __('main.database') }}:</span>
                        <span class="text-sm font-medium">{{ config('database.default') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ __('main.environment') }}:</span>
                        <span class="text-sm font-medium">
                            <span class="px-2 py-1 rounded text-xs {{ app()->environment('production') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ app()->environment() }}
                            </span>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ __('main.memory_usage') }}:</span>
                        <span class="text-sm font-medium">{{ round(memory_get_usage(true) / 1024 / 1024, 2) }} MB</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ __('main.execution_time') }}:</span>
                        <span class="text-sm font-medium">{{ round(microtime(true) - LARAVEL_START, 3) }}s</span>
                    </div>
                </div>

                <!-- Состояние очередей -->
                <div class="mt-6 pt-4 border-t">
                    <h4 class="font-medium text-gray-700 mb-2">{{ __('main.system_status') }}</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ __('main.queues') }}:</span>
                            <span class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-green-600">{{ __('main.working') }}</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ __('main.cache') }}:</span>
                            <span class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-green-600">{{ __('main.active') }}</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Email:</span>
                            <span class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-green-600">{{ __('main.configured') }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Популярные категории -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">🔥 {{ __('main.popular_categories') }}</h3>
            </div>
            <div class="p-6">
                @if(isset($popularCategories) && $popularCategories->count() > 0)
                    <div class="space-y-3">
                        @foreach($popularCategories as $category)
                            <div class="flex items-center justify-between">
                                <div>
                                    <a href="{{ route('categories.show', $category) }}" 
                                       class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $category->name }}
                                    </a>
                                    <div class="text-xs text-gray-500">
                                        {{ $category->posts_count ?? 0 }} {{ __('main.posts') }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium">{{ $category->topics_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">{{ __('main.topics') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        <div class="text-4xl mb-2">📁</div>
                        <p>{{ __('main.no_categories') }}</p>
                        <a href="{{ route('admin.categories.create') }}" 
                           class="mt-2 inline-block text-blue-600 hover:text-blue-800 text-sm">
                            {{ __('main.create_first_category') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Последние жалобы -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-700">⚠️ {{ __('main.recent_reports') }}</h3>
                    <a href="{{ route('admin.reports.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        {{ __('main.all_reports') }} →
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if(isset($recentReports) && $recentReports->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentReports as $report)
                            <div class="border border-gray-200 rounded p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $report->reason }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $report->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ __('main.from') }}: {{ $report->user ? $report->user->name : __('main.unknown') }}
                                    @if($report->post)
                                        • <a href="{{ route('topics.show', $report->post->topic) }}#post-{{ $report->post->id }}" 
                                             class="text-blue-500 hover:underline">{{ __('main.go_to_post') }}</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        <div class="text-4xl mb-2">✅</div>
                        <p>{{ __('main.no_new_reports') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Автообновление статистики каждые 30 секунд
setInterval(function() {
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Обновляем только числа статистики
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const statsElements = document.querySelectorAll('[data-stat]');
        statsElements.forEach(element => {
            const statType = element.getAttribute('data-stat');
            const newElement = doc.querySelector(`[data-stat="${statType}"]`);
            if (newElement) {
                element.textContent = newElement.textContent;
            }
        });
    })
    .catch(error => console.error('{{ __("main.stats_update_error") }}:', error));
}, 30000);
</script>
@endpush
@endsection