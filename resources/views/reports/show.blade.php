@extends('layouts.app')

@section('title', __('main.report_details') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-700">
                    ⚠️ {{ __('main.report_details') }} #{{ $report->id }}
                </h1>
                <p class="text-gray-600 mt-2">
                    {{ __('main.submitted') }}: {{ $report->created_at->format('d.m.Y H:i') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Статус жалобы -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">📊 {{ __('main.status') }}</h2>
        
        <div class="flex items-center gap-4">
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $report->status === 'pending' ? 'bg-orange-100 text-orange-800' : '' }}
                {{ $report->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                {{ $report->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                @if($report->status === 'pending')
                    ⏳ {{ __('main.pending') }}
                @elseif($report->status === 'resolved')
                    ✅ {{ __('main.resolved') }}
                @else
                    ❌ {{ __('main.rejected') }}
                @endif
            </span>
            
            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">
                {{ __('main.reason') }}: {{ $report->reason }}
            </span>
        </div>
    </div>

    <!-- Объект жалобы -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">🎯 {{ __('main.report_object') }}</h2>
        
        @if($report->reportable)
            @if($report->reportable instanceof \App\Models\Post)
                <div class="border border-gray-200 rounded p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium">{{ __('main.post_from') }} {{ $report->reportable->user->name }}</span>
                        <a href="{{ route('topics.show', [$report->reportable->topic, 'post' => $report->reportable->id]) }}#post-{{ $report->reportable->id }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            {{ __('main.go_to') }} →
                        </a>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ __('main.in_topic') }}: <span class="font-medium">{{ $report->reportable->topic->title }}</span>
                    </div>
                    <div class="text-sm text-gray-700 mt-3 bg-gray-50 p-3 rounded">
                        {{ Str::limit(strip_tags($report->reportable->content), 200) }}
                    </div>
                </div>
            @elseif($report->reportable instanceof \App\Models\Topic)
                <div class="border border-gray-200 rounded p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium">{{ __('main.topic_from') }} {{ $report->reportable->user->name }}</span>
                        <a href="{{ route('topics.show', $report->reportable) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            {{ __('main.go_to') }} →
                        </a>
                    </div>
                    <div class="text-sm font-medium text-gray-700">{{ $report->reportable->title }}</div>
                </div>
            @endif
        @else
            <div class="text-gray-500 text-center py-4">
                {{ __('main.content_not_available') }}
            </div>
        @endif
    </div>

    <!-- Описание жалобы -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">📝 {{ __('main.description') }}</h2>
        
        <div class="bg-gray-50 p-4 rounded text-gray-700">
            {{ $report->description }}
        </div>
    </div>

    @if($report->status !== 'pending')
        <!-- Решение модератора -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                👨‍⚖️ {{ __('main.moderator_decision') }}
            </h2>
            
            <div class="flex items-center mb-4">
                @if($report->moderator->avatar)
                    <img src="{{ $report->moderator->avatar_url }}" 
                         alt="{{ $report->moderator->name }}" 
                         class="w-8 h-8 rounded-full object-cover mr-3">
                @else
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                        {{ strtoupper(substr($report->moderator->name, 0, 1)) }}
                    </div>
                @endif
                
                <div>
                    <span class="font-medium">{{ $report->moderator->name }}</span>
                    <div class="text-xs text-gray-500">
                        {{ $report->resolved_at->format('d.m.Y H:i') }}
                    </div>
                </div>
            </div>
            
            @if($report->moderator_notes)
                <div class="bg-blue-50 border border-blue-100 rounded p-4 text-blue-700">
                    {{ $report->moderator_notes }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection 