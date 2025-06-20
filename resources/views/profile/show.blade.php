@extends('layouts.app')

@section('title', __('main.profile_of') . ': ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto" data-user-id="{{ $user->id }}">

    <!-- Основная информация о пользователе -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
            <!-- Аватар -->
            <div class="flex-shrink-0">
                @if($user->avatar)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                         class="w-32 h-32 rounded-full object-cover mx-auto">
                @else
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-4xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Информация о пользователе -->
            <div class="flex-grow">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h1>
                        @if($user->title)
                            <p class="text-lg text-blue-600 font-medium">{{ $user->title }}</p>
                        @endif
                        @if($user->role)
                            <p class="text-sm font-semibold {{ $user->role === 'admin' ? 'text-red-600' : ($user->role === 'moderator' ? 'text-orange-600' : 'text-gray-600') }}">
                                {{ ucfirst($user->role) }}
                                @if($user->role === 'admin')
                                    👑
                                @elseif($user->role === 'moderator')
                                    🛡️
                                @endif
                            </p>
                        @endif
                    </div>

                    <!-- Кнопки действий -->
                    <div class="flex gap-2 mt-4 sm:mt-0">
                        @auth
                            @if(auth()->id() !== $user->id)
                                <a href="{{ route('messages.start-with', $user) }}" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    💌 {{ __('main.send_message') }}
                                </a>
                            @endif

                            @if(auth()->id() === $user->id)
                                <a href="{{ route('profile.edit') }}" 
                                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    ⚙️ {{ __('main.edit_profile') }}
                                </a>
                            @endif

                            @if(auth()->user()->can('manage users') && auth()->id() !== $user->id)
                                <div class="relative group">
                                    <button class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                                        🛠️ {{ __('main.moderation') }}
                                    </button>
                                    <div class="absolute right-0 top-full mt-1 bg-white shadow-lg rounded-md py-1 w-48 z-10 hidden group-hover:block">
                                        @if(!$user->banned_until)
                                            <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="block">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                    🚫 {{ __('main.ban_user') }}
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="block">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                    ✅ {{ __('main.unban_user') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Статус пользователя -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                        <span class="w-3 h-3 {{ $user->isOnline() ? 'bg-green-500' : 'bg-gray-400' }} rounded-full mr-2"></span>
                        {{ $user->isOnline() ? __('main.online') : __('main.last_seen') . ' ' . $user->last_activity_at?->diffForHumans() }}
                    </div>
                    
                    <div>📅 {{ __('main.registered') }}: {{ $user->created_at->format('d.m.Y') }}</div>
                </div>

                <!-- Подпись -->
                @if($user->signature)
                    <div class="mt-4 p-3 bg-gray-50 border-l-4 border-blue-500 rounded">
                        <p class="text-sm text-gray-700 italic">{!! nl2br(e($user->signature)) !!}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Статус бана -->
        @if($user->banned_until)
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h3 class="font-medium text-red-800 mb-2">🚫 {{ __('main.user_banned') }}</h3>
                <p class="text-sm text-red-700">
                    {{ __('main.until') }}: {{ $user->banned_until->format('d.m.Y H:i') }}
                    @if($user->ban_reason)
                        <br>{{ __('main.reason') }}: {{ $user->ban_reason }}
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $user->posts_count ?? $user->posts->count() }}</div>
            <div class="text-sm text-gray-600">{{ __('main.posts') }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-green-600">{{ $user->topics_count ?? $user->topics->count() }}</div>
            <div class="text-sm text-gray-600">{{ __('main.topics') }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $user->likes_received ?? 0 }}</div>
            <div class="text-sm text-gray-600">{{ __('main.likes') }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $user->reputation ?? 0 }}</div>
            <div class="text-sm text-gray-600">{{ __('main.reputation') }}</div>
        </div>
    </div>

    <!-- Последние сообщения -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">💬 {{ __('main.recent_posts') }}</h3>
        </div>
        
        <div class="p-6">
            @if($recentPosts && $recentPosts->count() > 0)
                <div class="space-y-4">
                    @foreach($recentPosts as $post)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium">
                                    <a href="{{ route('topics.show', $post->topic) }}#post-{{ $post->id }}" 
                                       class="text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $post->topic->title }}
                                    </a>
                                </h4>
                                <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                {{ __('main.in_category') }}: <a href="{{ route('categories.show', $post->topic->category) }}" 
                                              class="text-blue-500 hover:underline">{{ $post->topic->category->name }}</a>
                            </div>
                            <div class="text-sm text-gray-700">
                                {{ Str::limit(strip_tags($post->content), 200) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 py-8">
                    <p>{{ __('main.user_no_posts_yet') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection