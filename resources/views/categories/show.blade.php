@extends('layouts.app')

@section('title', $category->name . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-700 {{ $category->is_rtl ? 'rtl arabic-text' : '' }}">
                    {{ $category->name }}
                    @if($category->name_ar)
                        <span class="arabic-text">({{ $category->name_ar }})</span>
                    @endif
                </h1>
                @if($category->description)
                    <p class="text-gray-600 mt-2 {{ $category->is_rtl ? 'rtl arabic-text' : '' }}">
                        {{ $category->description }}
                        @if($category->description_ar)
                            <span class="arabic-text">({{ $category->description_ar }})</span>
                        @endif
                    </p>
                @endif
            </div>
            
            @auth
                @if(auth()->user()->is_admin || auth()->user()->is_moderator)
                    <a href="{{ route('topics.create', $category) }}" 
                       class="mt-4 md:mt-0 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow hover:shadow-md">
                        + {{ __('main.new_topic') }}
                    </a>
                @else
                    <div class="mt-4 md:mt-0 text-sm text-gray-500">
                        {{ __('main.only_moderators_create_topics') }}
                    </div>
                @endif
            @else
                <div class="mt-4 md:mt-0 text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">{{ __('auth.login') }}</a> {{ __('main.to_access_forum') }}
                </div>
            @endauth
        </div>

        @if($topics->count() > 0)
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <!-- Header только для десктопа -->
                <div class="hidden md:grid grid-cols-12 gap-4 p-4 bg-gray-100 border-b border-gray-200 font-semibold text-gray-600 text-sm">
                    <div class="col-span-6">{{ __('main.topic_and_author') }}</div>
                    <div class="col-span-2 text-center">{{ __('main.replies') }}</div>
                    <div class="col-span-1 text-center">{{ __('main.views') }}</div>
                    <div class="col-span-3">{{ __('main.last_post') }}</div>
                </div>

                @foreach($topics as $topic)
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border-b border-gray-200 items-center topic-row 
                                {{ $topic->is_pinned ? 'bg-yellow-50' : '' }}
                                {{ $topic->is_locked ? 'bg-red-50' : '' }}">
                        
                        <!-- Название темы и автор -->
                        <div class="col-span-12 md:col-span-6">
                            <div class="flex items-center">
                                @if($topic->is_pinned)
                                    <span class="mr-2 text-yellow-500" title="{{ __('main.pinned_topic') }}">
                                        📌
                                    </span>
                                @endif
                                
                                @if($topic->is_locked)
                                    <span class="mr-2 text-red-500" title="{{ __('main.locked_topic') }}">
                                        🔒
                                    </span>
                                @endif
                                
                                <div class="flex-grow">
                                    <a href="{{ route('topics.show', $topic) }}" 
                                       class="font-semibold text-lg text-blue-600 hover:text-blue-700 transition-colors">
                                        {{ $topic->title }}
                                    </a>
                                    
                                    @if($topic->title_ar)
                                        <div class="text-sm text-gray-500 arabic-text mt-1">{{ $topic->title_ar }}</div>
                                    @endif
                                    
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ __('main.author') }}: 
                                        <a href="{{ route('profile.show', $topic->user) }}" class="text-blue-500 hover:underline">
                                            {{ $topic->user->name }}
                                        </a>
                                        • {{ $topic->created_at->format('d.m.Y, H:i') }}
                                    </p>
                                    
                                    @if($topic->tags && $topic->tags->count() > 0)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($topic->tags as $tag)
                                                <span class="px-2 py-1 bg-gray-200 text-gray-600 text-xs rounded">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ответы -->
                        <div class="col-span-6 md:col-span-2 text-left md:text-center text-sm text-gray-700">
                            <span class="md:hidden font-semibold">{{ __('main.replies') }}: </span>
                            {{ $topic->posts_count ?? 0 }}
                        </div>
                        
                        <!-- Просмотры -->
                        <div class="col-span-6 md:col-span-1 text-left md:text-center text-sm text-gray-700">
                            <span class="md:hidden font-semibold">{{ __('main.views') }}: </span>
                            {{ $topic->views_count ?? 0 }}
                        </div>
                        
                        <!-- Последнее сообщение -->
                        <div class="col-span-12 md:col-span-3 text-sm text-gray-500">
                            @php
                                $lastPost = $topic->posts()->latest()->first();
                            @endphp
                            
                            @if($lastPost && $lastPost->id !== $topic->posts()->oldest()->first()->id)
                                <p>
                                    <a href="{{ route('topics.show', $topic) }}#post-{{ $lastPost->id }}" 
                                       class="text-blue-500 hover:underline">
                                        Re: {{ Str::limit($topic->title, 30) }}
                                    </a>
                                </p>
                                <p>
                                    {{ __('main.from') }} <a href="{{ route('profile.show', $lastPost->user) }}" class="text-blue-500 hover:underline">
                                        {{ $lastPost->user->name }}
                                    </a>
                                </p>
                                <p class="text-xs">{{ $lastPost->created_at->format('d.m.Y, H:i') }}</p>
                            @else
                                <p class="text-gray-400">{{ __('main.no_replies') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            @if($topics->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $topics->links() }}
                </div>
            @endif
            
        @else
            <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">{{ __('main.no_topics_in_category') }}</h2>
                <p class="text-gray-600 mb-6">{{ __('main.be_first_to_create_topic') }}</p>
                
                @auth
                    @if(auth()->user()->is_admin || auth()->user()->is_moderator)
                        <a href="{{ route('topics.create', $category) }}" 
                           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            {{ __('main.create_first_topic') }}
                        </a>
                    @else
                        <p class="text-gray-500">
                            {{ __('main.only_moderators_create_topics') }}
                        </p>
                    @endif
                @else
                    <p class="text-gray-500">
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">{{ __('auth.login') }}</a> 
                        {{ __('main.to_access_forum') }}
                    </p>
                @endauth
            </div>
        @endif

        <!-- Модераторы категории -->
        @if($category->moderators && $category->moderators->count() > 0)
            <div class="mt-6 bg-white p-4 rounded-lg shadow">
                <h3 class="font-semibold text-gray-700 mb-2">{{ __('main.moderators') }}:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($category->moderators as $moderator)
                        <a href="{{ route('profile.show', $moderator) }}" 
                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full hover:bg-blue-200 transition-colors">
                            👮 {{ $moderator->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Подсветка новых тем (если были добавлены недавно)
    const recentTopics = document.querySelectorAll('.topic-row');
    recentTopics.forEach(row => {
        const timeText = row.querySelector('.text-xs')?.textContent;
        if (timeText && timeText.includes('{{ now()->format("d.m.Y") }}')) {
            row.classList.add('bg-blue-50', 'transition-colors', 'duration-1000');
            
            setTimeout(() => {
                row.classList.remove('bg-blue-50');
            }, 5000);
        }
    });
});
</script>
@endpush
@endsection