@extends('layouts.app')

@php
    $otherUser = auth()->id() === $message->sender_id ? $message->recipient : $message->sender;
@endphp

@section('title', __('main.conversation_with') . ' ' . $otherUser->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Боковая панель -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">💌 {{ __('main.actions') }}</h2>
                
                <div class="space-y-2">
                    <a href="{{ route('messages.create', ['to' => $otherUser->id]) }}" 
                       class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                        ✉️ {{ __('main.new_message') }}
                    </a>
                    
                    <button onclick="deleteConversation()" 
                            class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                        🗑️ {{ __('main.delete') }}
                    </button>
                </div>
                
                <div class="mt-6 pt-4 border-t">
                    <a href="{{ route('messages.index') }}" 
                       class="text-gray-600 hover:text-gray-800 text-sm">
                        ← {{ __('main.back') }}
                    </a>
                </div>
            </div>

            <!-- Информация о собеседнике -->
            <div class="bg-white rounded-lg shadow-lg p-4">
                <h3 class="text-md font-semibold text-gray-700 mb-3">👤 {{ __('main.user') }}</h3>
                
                <div class="text-center">
                    <a href="{{ route('profile.show', $otherUser) }}" class="block">
                        @if($otherUser->avatar)
                            <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->name }}" 
                                 class="w-16 h-16 rounded-full object-cover mx-auto mb-3">
                        @else
                            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-3">
                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                            </div>
                        @endif
                        <h4 class="font-medium text-blue-600 hover:text-blue-800">{{ $otherUser->name }}</h4>
                    </a>
                    
                    @if($otherUser->signature)
                        <p class="text-xs text-gray-500 mt-1">{{ $otherUser->signature }}</p>
                    @endif
                    
                    <div class="text-xs text-gray-500 mt-2 space-y-1">
                        <div class="flex items-center justify-center">
                            <span class="w-2 h-2 {{ $otherUser->isOnline() ? 'bg-green-500' : 'bg-gray-400' }} rounded-full mr-1"></span>
                            {{ $otherUser->isOnline() ? __('main.online') : __('main.offline') }}
                        </div>
                        
                        @if($otherUser->country)
                            <p>🌎 {{ $otherUser->country }}</p>
                        @endif
                        
                        <p>{{ __('main.posts') }}: {{ $otherUser->posts_count ?? $otherUser->posts->count() }}</p>
                        <p>{{ __('main.joined') }}: {{ $otherUser->created_at->format('d.m.Y') }}</p>
                    </div>
                    
                    @if(auth()->id() !== $otherUser->id)
                        <a href="{{ route('profile.show', $otherUser) }}" 
                           class="inline-block mt-3 px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded hover:bg-gray-200 transition-colors">
                            {{ __('main.view_profile') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Основное содержимое -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-lg flex flex-col h-full">
                <!-- Заголовок чата -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 mr-3">
                            @if($otherUser->avatar)
                                <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->name }}" 
                                     class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">{{ $otherUser->name }}</h2>
                            <div class="text-xs text-gray-500 flex items-center">
                                <span class="w-2 h-2 {{ $otherUser->isOnline() ? 'bg-green-500' : 'bg-gray-400' }} rounded-full mr-1"></span>
                                {{ $otherUser->isOnline() ? __('main.online') : __('main.last_seen') . ' ' . ($otherUser->last_activity_at?->diffForHumans() ?? __('main.long_time_ago')) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Сообщения -->
                <div class="flex-grow p-4 overflow-y-auto" id="messages-container" style="max-height: 600px;">
                    <div class="space-y-4">
                        @if(isset($messages) && $messages->count() > 0)
                            @php
                                $prevDate = null;
                                $prevSenderId = null;
                            @endphp
                            
                            @foreach($messages as $message)
                                @php
                                    $currentDate = $message->created_at->format('Y-m-d');
                                    $showDate = $prevDate !== $currentDate;
                                    $prevDate = $currentDate;
                                    
                                    $isMine = $message->sender_id === auth()->id();
                                    $showAvatar = $prevSenderId !== $message->sender_id;
                                    $prevSenderId = $message->sender_id;
                                @endphp
                                
                                @if($showDate)
                                    <div class="flex justify-center my-4">
                                        <div class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">
                                            {{ $message->created_at->format('d F Y') }}
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="message flex {{ $isMine ? 'justify-end' : 'justify-start' }} {{ $showAvatar ? 'mt-4' : 'mt-1' }}">
                                    @if(!$isMine && $showAvatar)
                                        <div class="flex-shrink-0 mr-2">
                                            @if($message->sender->avatar)
                                                <img src="{{ $message->sender->avatar_url }}" alt="{{ $message->sender->name }}" 
                                                     class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                                    {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    @elseif(!$isMine && !$showAvatar)
                                        <div class="flex-shrink-0 mr-2 w-8"></div>
                                    @endif
                                    
                                    <div class="max-w-[70%]">
                                        <div class="{{ $isMine ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }} rounded-lg px-4 py-2 message-bubble {{ $isMine ? 'rounded-tr-none' : 'rounded-tl-none' }}">
                                            <div class="text-sm whitespace-pre-wrap">{{ $message->content }}</div>
                                        </div>
                                        <div class="text-xs {{ $isMine ? 'text-right' : 'text-left' }} text-gray-500 mt-1">
                                            {{ $message->created_at->format('H:i') }}
                                            @if($isMine && $message->is_read)
                                                <span class="text-blue-500 ml-1">✓</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($isMine && $showAvatar)
                                        <div class="flex-shrink-0 ml-2">
                                            @if(auth()->user()->avatar)
                                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" 
                                                     class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($isMine && !$showAvatar)
                                        <div class="flex-shrink-0 ml-2 w-8"></div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <p>{{ __('main.no_messages_yet') }}</p>
                                <p class="mt-2">{{ __('main.start_conversation') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Форма отправки сообщения -->
                <div class="border-t border-gray-200 p-4">
                    <form method="POST" action="{{ route('messages.reply', $conversation) }}" id="reply-form">
                        @csrf
                        <div class="flex items-end">
                            <div class="flex-grow mr-2">
                                <textarea name="body" rows="1" placeholder="{{ __('main.write_message') }}..."
                                          class="w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 px-3 py-2 resize-none"
                                          id="message-input"></textarea>
                            </div>
                            <button type="submit" class="bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Скрытые формы -->
<form id="delete-form" method="POST" action="{{ route('messages.destroy', $conversation) }}" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
// Автоматическая прокрутка чата вниз
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
}

// Вызов при загрузке
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    
    // Фокус на поле ввода
    document.getElementById('message-input').focus();
});

// Удаление беседы
function deleteConversation() {
    if (confirm('{{ __("main.confirm_delete_conversation") }}')) {
        document.getElementById('delete-form').submit();
    }
}

// Автоматическое изменение размера textarea
const messageInput = document.getElementById('message-input');
messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 150) + 'px';
});

// Отправка сообщения по Ctrl+Enter
messageInput.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('reply-form').submit();
    }
});

// Отслеживание времени чтения
let readingStartTime = Date.now();
window.addEventListener('beforeunload', function() {
    const readingTime = Math.floor((Date.now() - readingStartTime) / 1000);
    if (readingTime > 5) { // Если читал больше 5 секунд
        navigator.sendBeacon('{{ route("messages.track-reading-time", $conversation) }}', 
            JSON.stringify({ reading_time: readingTime }));
    }
});
</script>
@endpush

@push('styles')
<style>
.message-bubble {
    position: relative;
    word-wrap: break-word;
}

.message-bubble-tail {
    position: absolute;
    width: 0;
    height: 0;
    border: 10px solid transparent;
}

.whitespace-pre-wrap {
    white-space: pre-wrap;
}

/* Анимация появления сообщений */
.message {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Стили для полосы прокрутки */
#messages-container::-webkit-scrollbar {
    width: 6px;
}

#messages-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#messages-container::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
}

#messages-container::-webkit-scrollbar-thumb:hover {
    background: #ccc;
}
</style>
@endpush
@endsection