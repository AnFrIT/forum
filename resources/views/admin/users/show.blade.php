@extends('layouts.app')

@section('title', __('main.user_profile') . ': ' . $user->name . ' - ' . config('app.name'))

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Информация о пользователе -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="text-center mb-6">
                    @if($user->avatar)
                        <img src="{{ $user->avatar_url }}" alt="{{ __('main.avatar') }}" 
                             class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-gray-200">
                    @else
                        <div class="w-32 h-32 bg-blue-500 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-4xl font-bold border-4 border-gray-200">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    
                    <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    
                    <!-- Роли -->
                    <div class="mt-3 flex justify-center flex-wrap gap-2">
                        @if($user->role === 'admin')
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                                👑 {{ __('main.admin') }}
                            </span>
                        @elseif($user->role === 'moderator')
                            <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full">
                                🛡️ {{ __('main.moderator') }}
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                                👤 {{ __('main.user') }}
                            </span>
                        @endif

                        @if($user->isOnline())
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                🟢 {{ __('main.online') }}
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                                ⚫ {{ __('main.offline') }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Статус блокировки -->
                    @if($user->banned_until)
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center justify-center text-red-600 font-medium">
                                🚫 {{ __('main.banned') }}
                            </div>
                            @if($user->banned_until)
                                <div class="text-sm text-red-500 mt-1">
                                    {{ __('main.until') }}: {{ $user->banned_until->format('d.m.Y H:i') }}
                                </div>
                            @endif
                            @if($user->ban_reason)
                                <div class="text-sm text-red-500 mt-1">
                                    {{ __('main.reason') }}: {{ $user->ban_reason }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Основная информация -->
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('main.user_id') }}:</span>
                        <span class="font-medium">{{ $user->id }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('auth.email') }}:</span>
                        <div class="text-right">
                            <span class="font-medium">{{ $user->email }}</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('main.registration') }}:</span>
                        <span class="font-medium">{{ $user->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('main.last_activity') }}:</span>
                        <span class="font-medium">
                            @if($user->last_activity_at)
                                {{ $user->last_activity_at->diffForHumans() }}
                            @else
                                {{ __('main.no_data') }}
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('main.topics_created') }}:</span>
                        <span class="font-medium text-blue-600">{{ $stats['topics_count'] ?? 0 }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('main.posts_written') }}:</span>
                        <span class="font-medium text-green-600">{{ $stats['posts_count'] ?? 0 }}</span>
                    </div>
                </div>

                <!-- Действия -->
                <div class="mt-6 space-y-2">
                    <a href="{{ route('admin.users.edit', $user) }}" 
                       class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-center block">
                        ✏️ {{ __('main.edit') }}
                    </a>
                    
                    @if($user->id !== auth()->id())
                        @if($user->banned_until)
                            <form action="{{ route('admin.users.unban', $user) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                    ✅ {{ __('main.unban') }}
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="showBanModal()" 
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                🚫 {{ __('main.ban') }}
                            </button>
                        @endif
                        
                        @if($user->role !== 'admin')
                            @if($user->role === 'moderator')
                                <form action="{{ route('admin.users.remove-moderator', $user) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                                        🛡️➖ {{ __('main.remove_moderator') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.add-moderator', $user) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                                        🛡️➕ {{ __('main.make_moderator') }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Активность пользователя -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Последние темы -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">📝 {{ __('main.recent_topics') }}</h3>
                </div>
                <div class="p-6">
                    @if(isset($recentTopics) && $recentTopics->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentTopics as $topic)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-grow">
                                            <h4 class="font-medium text-gray-800 mb-2">
                                                <a href="{{ route('topics.show', $topic) }}" 
                                                   class="text-blue-600 hover:text-blue-800 hover:underline" 
                                                   target="_blank">
                                                    {{ $topic->title }}
                                                </a>
                                            </h4>
                                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                                <span>📁 {{ $topic->category->name }}</span>
                                                <span>📅 {{ $topic->created_at->format('d.m.Y H:i') }}</span>
                                                <span>💬 {{ $topic->posts_count ?? 0 }} {{ __('main.replies') }}</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end space-y-1 ml-4">
                                            @if($topic->is_pinned)
                                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">📌 {{ __('main.pinned') }}</span>
                                            @endif
                                            @if($topic->is_locked)
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">🔒 {{ __('main.locked') }}</span>
                                            @endif
                                            @if(!$topic->is_approved)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">⏳ {{ __('main.pending_approval') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📝</div>
                            <p>{{ __('main.user_no_topics') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Последние сообщения -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">💬 {{ __('main.recent_posts') }}</h3>
                </div>
                <div class="p-6">
                    @if(isset($recentPosts) && $recentPosts->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentPosts as $post)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-medium text-gray-800">
                                                <a href="{{ route('topics.show', $post->topic) }}#post-{{ $post->id }}" 
                                                   class="text-blue-600 hover:text-blue-800 hover:underline" 
                                                   target="_blank">
                                                    {{ $post->topic->title }}
                                                </a>
                                            </h4>
                                            <span class="text-sm text-gray-500">{{ $post->created_at->format('d.m.Y H:i') }}</span>
                                        </div>
                                        <div class="text-gray-700 text-sm leading-relaxed">
                                            {{ Str::limit(strip_tags($post->content), 200) }}
                                        </div>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500 space-x-4">
                                        <span>📁 {{ $post->topic->category->name }}</span>
                                        <span>👁️ {{ $post->views ?? 0 }} {{ __('main.views') }}</span>
                                        @if($post->likes_count > 0)
                                            <span>👍 {{ $post->likes_count }} {{ __('main.likes') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">💬</div>
                            <p>{{ __('main.user_no_posts') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Статистика активности -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">📊 {{ __('main.activity_stats') }}</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['topics_count'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">{{ __('main.topics_created') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['posts_count'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">{{ __('main.posts_written') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['likes_received'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">{{ __('main.likes_received') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $stats['days_since_registration'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">{{ __('main.days_registered') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно блокировки -->
@if($user->id !== auth()->id() && !$user->banned_until)
    <div id="ban-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-red-600 mb-4">🚫 {{ __('main.ban_user') }}</h3>
            
            <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('main.ban_reason') }} *
                    </label>
                    <input type="text" name="reason" id="reason" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="{{ __('main.enter_ban_reason') }}">
                </div>
                
                <div class="mb-4">
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('main.duration_days') }}
                    </label>
                    <input type="number" name="duration" id="duration" min="1" max="3650"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="{{ __('main.leave_empty_permanent') }}">
                    <p class="text-xs text-gray-500 mt-1">{{ __('main.leave_empty_permanent_ban') }}</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideBanModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        {{ __('main.cancel') }}
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        🚫 {{ __('main.ban') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@push('scripts')
<script>
function showBanModal() {
    document.getElementById('ban-modal').classList.remove('hidden');
    document.getElementById('ban-modal').classList.add('flex');
}

function hideBanModal() {
    document.getElementById('ban-modal').classList.add('hidden');
    document.getElementById('ban-modal').classList.remove('flex');
}

// Закрытие модального окна по клику вне его
document.getElementById('ban-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideBanModal();
    }
});
</script>
@endpush
@endsection