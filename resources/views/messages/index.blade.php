@extends('layouts.app')

@section('title', __('main.private_messages') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-700">{{ __('main.private_messages') }}</h1>
            </div>
        </div>

        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Боковая панель -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">💌 {{ __('main.messages') }}</h2>
                        
                        <div class="space-y-2">
                            <a href="{{ route('messages.index') }}" 
                               class="flex items-center justify-between p-3 rounded-lg {{ !request('folder') || request('folder') === 'inbox' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                <div class="flex items-center">
                                    <span class="mr-2">📥</span>
                                    <span>{{ __('main.inbox') }}</span>
                                </div>
                                @if(isset($inboxCount) && $inboxCount > 0)
                                    <span class="bg-blue-600 text-white text-xs rounded-full px-2 py-1">{{ $inboxCount }}</span>
                                @endif
                            </a>
                            
                            <a href="{{ route('messages.index', ['folder' => 'archived']) }}" 
                               class="flex items-center justify-between p-3 rounded-lg {{ request('folder') === 'archived' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                <div class="flex items-center">
                                    <span class="mr-2">📦</span>
                                    <span>{{ __('main.archived') }}</span>
                                </div>
                                @if(isset($archivedCount) && $archivedCount > 0)
                                    <span class="bg-gray-500 text-white text-xs rounded-full px-2 py-1">{{ $archivedCount }}</span>
                                @endif
                            </a>
                            
                            <a href="{{ route('messages.index', ['folder' => 'deleted']) }}" 
                               class="flex items-center justify-between p-3 rounded-lg {{ request('folder') === 'deleted' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                <div class="flex items-center">
                                    <span class="mr-2">🗑️</span>
                                    <span>{{ __('main.deleted') }}</span>
                                </div>
                                @if(isset($deletedCount) && $deletedCount > 0)
                                    <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $deletedCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Быстрые контакты -->
                    @if(isset($recentContacts) && $recentContacts->count() > 0)
                        <div class="bg-white rounded-lg shadow-lg p-4">
                            <h3 class="text-md font-semibold text-gray-700 mb-3">👥 {{ __('main.recent_contacts') }}</h3>
                            <div class="space-y-2">
                                @foreach($recentContacts as $contact)
                                    <a href="{{ route('messages.start-with', $contact) }}" 
                                       class="flex items-center p-2 rounded hover:bg-gray-50 transition-colors">
                                        @if($contact->avatar)
                                            <img src="{{ $contact->avatar_url }}" alt="{{ $contact->name }}" 
                                                 class="w-12 h-12 rounded-full object-cover mr-4">
                                        @else
                                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-4">
                                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="flex-grow">
                                            <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                                            <div class="text-xs text-gray-500">
                                                @if($contact->isOnline())
                                                    <span class="text-green-600">● {{ __('main.online') }}</span>
                                                @else
                                                    {{ $contact->last_activity_at?->diffForHumans() ?? __('main.long_time_ago') }}
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Основное содержимое -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-lg">
                        <!-- Заголовок -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-700">
                                        @if(!request('folder') || request('folder') === 'inbox')
                                            📥 {{ __('main.conversations') }}
                                        @elseif(request('folder') === 'archived')
                                            📦 {{ __('main.archived') }}
                                        @elseif(request('folder') === 'deleted')
                                            🗑️ {{ __('main.deleted') }}
                                        @endif
                                    </h1>
                                    @if(isset($conversations))
                                        <p class="text-gray-600 mt-1">{{ __('main.total_conversations', ['count' => $conversations->total()]) }}</p>
                                    @endif
                                </div>
                                
                                <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                                    <!-- Поиск -->
                                    <div class="relative">
                                        <form method="GET" action="{{ route('messages.index') }}">
                                            @if(request('folder'))
                                                <input type="hidden" name="folder" value="{{ request('folder') }}">
                                            @endif
                                            <input type="text" name="search" 
                                                   value="{{ request('search') }}"
                                                   placeholder="{{ __('main.search_messages') }}"
                                                   class="pl-8 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                                            <div class="absolute left-2 top-2.5 text-gray-400">🔍</div>
                                        </form>
                                    </div>
                                    
                                    <!-- Массовые действия -->
                                    <div class="flex items-center space-x-1">
                                        <button onclick="markAllAsRead()" 
                                                class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded" 
                                                title="{{ __('main.mark_read') }}">
                                            ✉️
                                        </button>
                                        <button onclick="archiveSelected()" 
                                                class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded" 
                                                title="{{ __('main.archive_selected') }}">
                                            📦
                                        </button>
                                        <button onclick="deleteSelected()" 
                                                class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded" 
                                                title="{{ __('main.delete_selected') }}">
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Список чатов -->
                        <div class="divide-y divide-gray-200">
                            @if(isset($conversations) && $conversations->count() > 0)
                                @foreach($conversations as $conversation)
                                    @php
                                        $otherUser = $conversation->getOtherUser(auth()->id());
                                        $latestMessage = $conversation->latestMessage;
                                        $unreadCount = $conversation->unreadCount(auth()->id());
                                    @endphp
                                    <div class="conversation-item flex items-center p-4 hover:bg-gray-50 transition-colors {{ $unreadCount > 0 ? 'bg-blue-50' : '' }}">
                                        <!-- Чекбокс -->
                                        <div class="w-8 flex-shrink-0">
                                            <input type="checkbox" name="selected_conversations[]" value="{{ $conversation->id }}" 
                                                   class="conversation-checkbox text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        </div>

                                        <!-- Аватар -->
                                        <div class="flex-shrink-0 mr-4">
                                            <a href="{{ route('messages.show', $conversation) }}" class="block">
                                                @if($otherUser->avatar)
                                                    <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->name }}" 
                                                         class="w-14 h-14 rounded-full object-cover">
                                                @else
                                                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                                                        <span class="text-lg font-medium text-blue-600">{{ strtoupper(substr($otherUser->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                            </a>
                                        </div>
                                        
                                        <!-- Информация о чате -->
                                        <div class="flex-grow min-w-0">
                                            <a href="{{ route('messages.show', $conversation) }}" class="block">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <h3 class="text-base font-semibold text-gray-900 {{ $unreadCount > 0 ? 'font-bold' : '' }}">
                                                            {{ $otherUser->name }}
                                                        </h3>
                                                        @if($otherUser->isOnline())
                                                            <span class="ml-2 w-2 h-2 bg-green-500 rounded-full"></span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '' }}
                                                    </div>
                                                </div>
                                                
                                                @if($latestMessage)
                                                    <div class="mt-1">
                                                        <p class="text-sm text-gray-600 truncate {{ $unreadCount > 0 ? 'font-medium' : '' }}">
                                                            @if($latestMessage->sender_id === auth()->id())
                                                                <span class="text-gray-400">{{ __('main.you') }}:</span>
                                                            @endif
                                                            {{ Str::limit(strip_tags($latestMessage->content), 50) }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </a>
                                        </div>
                                        
                                        <!-- Индикаторы -->
                                        <div class="ml-4 flex-shrink-0 flex flex-col items-end">
                                            @if($unreadCount > 0)
                                                <span class="bg-blue-600 text-white text-xs rounded-full px-2 py-1 mb-1">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                            
                                            <div class="flex space-x-1">
                                                <a href="{{ route('messages.show', $conversation) }}" 
                                                   class="p-1 text-gray-400 hover:text-blue-600" title="{{ __('main.view') }}">
                                                    👁️
                                                </a>
                                                <button onclick="deleteConversation({{ $conversation->id }})" 
                                                        class="p-1 text-gray-400 hover:text-red-600" title="{{ __('main.delete') }}">
                                                    🗑️
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Пагинация -->
                                @if($conversations->hasPages())
                                    <div class="p-6 border-t border-gray-200">
                                        {{ $conversations->appends(request()->query())->links() }}
                                    </div>
                                @endif
                            @else
                                <!-- Пустое состояние -->
                                <div class="p-12 text-center">
                                    <div class="text-6xl mb-4">
                                        @if(!request('folder') || request('folder') === 'inbox')
                                            📥
                                        @elseif(request('folder') === 'archived')
                                            📦
                                        @else
                                            🗑️
                                        @endif
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-700 mb-2">
                                        @if(request('search'))
                                            {{ __('main.no_results') }}
                                        @else
                                            {{ __('main.no_conversations') }}
                                        @endif
                                    </h3>
                                    <p class="text-gray-600 mb-6">
                                        @if(request('search'))
                                            Попробуйте изменить поисковый запрос или очистить фильтры
                                        @elseif(request('folder') === 'archived')
                                            У вас нет архивированных сообщений
                                        @elseif(request('folder') === 'deleted')
                                            Корзина пуста
                                        @else
                                            {{ __('main.no_conversations_desc') }}
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Выбрать все сообщения
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.conversation-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Отметить все как прочитанные
function markAllAsRead() {
    if (confirm('Отметить все сообщения как прочитанные?')) {
        fetch('{{ route("messages.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка при выполнении операции');
            }
        });
    }
}

// Архивировать выбранные
function archiveSelected() {
    const selected = getSelectedConversations();
    if (selected.length === 0) {
        alert('Выберите беседы для архивирования');
        return;
    }
    
    if (confirm(`Архивировать ${selected.length} бесед?`)) {
        performBulkAction('archive', selected);
    }
}

// Удалить выбранные
function deleteSelected() {
    const selected = getSelectedConversations();
    if (selected.length === 0) {
        alert('Выберите беседы для удаления');
        return;
    }
    
    if (confirm(`Удалить ${selected.length} бесед?`)) {
        performBulkAction('delete', selected);
    }
}

// Удалить одну беседу
function deleteConversation(id) {
    if (confirm('Удалить эту беседу?')) {
        fetch(`/messages/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Ошибка при удалении беседы');
            }
        });
    }
}

// Получить ID выбранных бесед
function getSelectedConversations() {
    const checkboxes = document.querySelectorAll('.conversation-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Выполнить массовое действие
function performBulkAction(action, conversationIds) {
    fetch('{{ route("messages.bulk-action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            action: action,
            conversation_ids: conversationIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Ошибка при выполнении операции');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при выполнении операции');
    });
}

// Автоматическая отправка поиска при изменении
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.closest('form').submit();
        }, 500);
    });
}

// Периодическое обновление счетчиков новых сообщений
setInterval(function() {
    fetch('{{ route("messages.check-new") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.has_new) {
            // Обновляем счетчики или показываем уведомление
            const inboxBadge = document.querySelector('a[href*="inbox"] .bg-blue-600');
            if (inboxBadge && data.unread_count) {
                inboxBadge.textContent = data.unread_count;
            }
        }
    })
    .catch(error => console.error('Error checking new messages:', error));
}, 30000); // Проверяем каждые 30 секунд
</script>
@endpush

@push('styles')
<style>
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.conversation-item:hover {
    transform: translateX(2px);
    transition: transform 0.2s ease;
}

.conversation-item {
    position: relative;
}

.conversation-item::after {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background-color: transparent;
    transition: background-color 0.2s ease;
}

.conversation-item:hover::after {
    background-color: #4f46e5;
}
</style>
@endpush
@endsection