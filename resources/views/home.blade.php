@extends('layouts.app')

@section('title', __('main.home') . ' - ' . ($siteName ?? config('app.name')))

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Welcome Section --}}
    <div class="bg-white rounded-lg shadow-lg p-8 mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-full h-full opacity-[0.03] flex justify-end items-center">
            <span class="arabic-logo text-[25rem] text-blue-900 transform -translate-y-12 translate-x-20">الإنصاف</span>
        </div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold text-blue-900 mb-4">
                {{ __('main.welcome_message') }}
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mb-6">
                {{ $siteDescription }}
            </p>
            <div class="text-blue-700 inline-flex items-center bg-blue-50/70 px-3 py-1.5 rounded-md font-medium backdrop-blur-sm border border-blue-100/50">
                <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Categories Section --}}
    <section class="mb-12">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-blue-900 relative">
                <span class="relative z-10">{{ __('main.forum_categories') }}</span>
                <span class="absolute bottom-0 left-0 h-3 bg-blue-200 w-full -z-10 opacity-50"></span>
            </h2>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        {{ __('main.create') }} {{ __('main.category') }}
                    </a>
                @endif
            @endauth
        </div>

        @if($categories->count() > 0)
            <div class="space-y-6">
                @foreach($categories as $category)
                    <div class="bg-white rounded-lg shadow-lg p-6 forum-card">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div class="flex-grow">
                                <div class="flex items-center mb-2">
                                    <h3 class="text-xl font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                                        <a href="{{ route('categories.show', $category) }}">
                                            {{ $category->name }}
                                        </a>
                                    </h3>
                                </div>
                                
                                @if($category->description)
                                    <p class="text-gray-600 text-sm mb-3">
                                        {{ $category->description }}
                                    </p>
                                @endif

                                @if($category->children && $category->children->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($category->children as $subcategory)
                                            <a href="{{ route('categories.show', $subcategory) }}" 
                                               class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
                                                {{ $subcategory->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-4 sm:mt-0 sm:ml-6 text-sm text-gray-500 min-w-0 sm:min-w-[200px]">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <div class="font-semibold text-lg text-blue-600">
                                            {{ $category->topics_count ?? 0 }}
                                        </div>
                                        <div class="text-xs uppercase tracking-wide">
                                            {{ __('main.topics_count') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-lg text-green-600">
                                            {{ $category->posts_count ?? 0 }}
                                        </div>
                                        <div class="text-xs uppercase tracking-wide">
                                            {{ __('main.posts_count') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- No Categories Message --}}
            <div class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">
                        {{ __('main.welcome_message') }}
                    </h3>
                    <p class="text-gray-500 mb-6">
                        {{ __('main.no_categories_yet') }}
                    </p>
                    
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                {{ __('main.create') }} {{ __('main.category') }}
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        @endif
    </section>

    {{-- Forum Statistics --}}
    <section class="bg-gradient-to-r from-blue-700 to-blue-900 rounded-lg shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-full h-full opacity-[0.04] flex justify-end items-center">
            <span class="arabic-logo text-[28rem] text-white transform -translate-y-16 translate-x-24">الإنصاف</span>
        </div>
        
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-8 border-b border-white/20 pb-3 inline-block">
                {{ __('main.statistics') }}
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-left border-l-2 border-white/30 pl-4">
                    <div class="text-3xl font-bold mb-1">
                        {{ $stats['topics'] ?? 0 }}
                    </div>
                    <div class="text-sm uppercase tracking-wider text-blue-100 font-light">
                        {{ __('main.topics') }}
                    </div>
                </div>
                
                <div class="text-left border-l-2 border-white/30 pl-4">
                    <div class="text-3xl font-bold mb-1">
                        {{ $stats['posts'] ?? 0 }}
                    </div>
                    <div class="text-sm uppercase tracking-wider text-blue-100 font-light">
                        {{ __('main.posts') }}
                    </div>
                </div>
                
                <div class="text-left border-l-2 border-white/30 pl-4">
                    <div class="text-3xl font-bold mb-1">
                        {{ $stats['users'] ?? 0 }}
                    </div>
                    <div class="text-sm uppercase tracking-wider text-blue-100 font-light">
                        {{ __('main.users') }}
                    </div>
                </div>
                
                <div class="text-left border-l-2 border-white/30 pl-4">
                    <div class="text-3xl font-bold mb-1">
                        {{ $stats['categories'] ?? 0 }}
                    </div>
                    <div class="text-sm uppercase tracking-wider text-blue-100 font-light">
                        {{ __('main.categories') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection