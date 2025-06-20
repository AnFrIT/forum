@extends('layouts.app')

@section('title', $topic->title . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Тема -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-700 mb-2">{{ $topic->title }}</h1>
            <div class="text-gray-600">
                <span>{{ __('main.category') }}:</span> 
                @if($topic->category->parent)
                    <a href="{{ route('categories.show', $topic->category->parent) }}" class="text-blue-600 hover:underline">
                        {{ $topic->category->parent->name }}
                    </a>
                    /
                @endif
                <a href="{{ route('categories.show', $topic->category) }}" class="text-blue-600 hover:underline">
                    {{ $topic->category->name }}
                </a>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">
            <div class="flex-grow">
                <h1 class="text-xl font-bold text-gray-700 flex items-center gap-2">
                    @if($topic->is_pinned)
                        <span class="text-yellow-500" title="{{ __('main.pinned_topic') }}">📌</span>
                    @endif
                    @if($topic->is_locked)
                        <span class="text-red-500" title="{{ __('main.locked_topic') }}">🔒</span>
                    @endif
                    {{ $topic->title }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-600">
                    <span>👤 {{ __('main.author') }}: <a href="{{ route('profile.show', $topic->user) }}" class="text-blue-600 hover:underline">{{ $topic->user->name }}</a></span>
                    <span>📅 {{ $topic->created_at->format('d.m.Y, H:i') }}</span>
                    <span>💬 {{ $posts->total() - 1 }} {{ trans_choice('messages.replies', $posts->total() - 1) }}</span>
                    <span>👀 {{ $topic->views_count ?? 0 }} {{ trans_choice('messages.views', $topic->views_count ?? 0) }}</span>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-2">
                @auth
                    @if(!$topic->is_locked || auth()->user()->can('moderate topics'))
                        <a href="#reply-form" 
                           class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            💬 {{ __('main.reply') }}
                        </a>
                    @endif
                    
                    @if($topic->canEdit())
                        <a href="{{ route('topics.edit', $topic) }}" 
                           class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                            ✏️ {{ __('main.edit') }}
                        </a>
                    @endif
                    
                    <button type="button" onclick="openReportForm('topic', {{ $topic->id }})" 
                            class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                        ⚠️ {{ __('main.report') }}
                    </button>
                    
                    @can('moderate topics')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('topics.pin', $topic) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-1.5 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700 transition-colors">
                                    {{ $topic->is_pinned ? '📌 ' . __('main.unpin') : '📌 ' . __('main.pin') }}
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('topics.lock', $topic) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                    {{ $topic->is_locked ? '🔓 ' . __('main.unlock') : '🔒 ' . __('main.lock') }}
                                </button>
                            </form>
                        </div>
                    @endcan
                @else
                    <div class="text-sm text-gray-500">
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">{{ __('auth.login') }}</a> {{ __('main.to_reply') }}
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Сообщения -->
    <div class="space-y-6">
        @foreach($posts as $index => $post)
            <div id="post-{{ $post->id }}" class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
                <div class="bg-gray-50 px-3 py-2 border-b flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">
                            #{{ ($posts->currentPage() - 1) * $posts->perPage() + $index + 1 }}
                        </span>
                        <span class="text-gray-500">{{ $post->created_at->format('d.m.Y, H:i') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        @auth
                            <button type="button" onclick="openReportForm('post', {{ $post->id }})" class="text-red-600 hover:text-red-800 text-xs">
                                ⚠️ {{ __('main.report') }}
                            </button>
                        @endauth
                    </div>
                </div>

                <div class="flex">
                    <!-- Информация о пользователе -->
                    <div class="w-48 p-3 border-r bg-gray-50">
                        <div class="flex flex-col items-center">
                            @if($post->user->avatar)
                                <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" 
                                     class="w-10 h-10 rounded-full object-cover mr-3">
                            @else
                                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                                    <span class="text-2xl text-blue-600">{{ strtoupper(substr($post->user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            
                            <a href="{{ route('profile.show', $post->user) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium text-sm truncate max-w-full">
                                {{ $post->user->name }}
                            </a>
                            
                            <div class="text-xs text-gray-500 mt-1">{{ $post->user->role_name ?? __('main.user') }}</div>
                            
                            <div class="text-xs text-gray-500 mt-2">
                                <div>{{ __('main.posts') }}: {{ $post->user->posts_count }}</div>
                                <div>{{ __('main.joined') }}: {{ $post->user->created_at->format('d.m.Y') }}</div>
                                @if($post->user->country)
                                    <div class="mt-1">🌎 {{ $post->user->country }}</div>
                                @endif
                            </div>
                            
                            @auth
                                @if(auth()->id() !== $post->user->id)
                                    <div class="mt-3">
                                        <a href="{{ route('messages.start-with', $post->user) }}" 
                                           class="text-xs text-blue-600 hover:underline">
                                            💌 {{ __('main.send_message') }}
                                        </a>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                    
                    <!-- Содержимое сообщения -->
                    <div class="flex-grow p-4">
                        <div class="prose max-w-none text-sm {{ $post->is_rtl ? 'rtl arabic-text' : '' }}">
                            {!! nl2br(e($post->content)) !!}
                            
                            @if($post->content_ar)
                                <div class="mt-3 arabic-text rtl border-t pt-3">
                                    {!! nl2br(e($post->content_ar)) !!}
                                </div>
                            @endif
                        </div>
                        
                        @if($post->attachments && $post->attachments->count() > 0)
                            <div class="mt-3 pt-3 border-t">
                                <h5 class="font-medium text-gray-700 text-sm mb-2">{{ __('main.attachments') }}:</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($post->attachments as $attachment)
                                        <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                            <div class="flex items-center mb-2">
                                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center text-xl mr-3">
                                                    @if($attachment->isImage())
                                                        🖼️
                                                    @elseif($attachment->isVideo())
                                                        🎬
                                                    @elseif(pathinfo($attachment->original_filename, PATHINFO_EXTENSION) == 'pdf')
                                                        📄
                                                    @elseif(in_array(pathinfo($attachment->original_filename, PATHINFO_EXTENSION), ['doc', 'docx']))
                                                        📝
                                                    @elseif(in_array(pathinfo($attachment->original_filename, PATHINFO_EXTENSION), ['zip', 'rar']))
                                                        📦
                                                    @else
                                                        📎
                                                    @endif
                                                </div>
                                                <div class="flex-grow min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->original_filename }}</p>
                                                    <p class="text-xs text-gray-500">{{ $attachment->formatted_size }}</p>
                                                </div>
                                                <a href="{{ $attachment->url }}" 
                                                   class="flex-shrink-0 ml-2 text-blue-600 hover:text-blue-800" 
                                                   target="_blank">
                                                    ⬇️
                                                </a>
                                            </div>
                                            
                                            @if($attachment->isImage())
                                                <div class="mt-2">
                                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" 
                                                         class="w-full h-32 object-cover rounded cursor-pointer"
                                                         onclick="showImageModal('{{ $attachment->url }}', '{{ $attachment->original_filename }}')">
                                                </div>
                                            @elseif($attachment->isVideo())
                                                <div class="mt-2">
                                                    <video controls class="w-full rounded" preload="metadata">
                                                        <source src="{{ $attachment->url }}" type="{{ $attachment->mime_type }}">
                                                        Ваш браузер не поддерживает воспроизведение видео.
                                                    </video>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if($post->user->signature)
                            <div class="mt-3 pt-3 border-t text-xs text-gray-500 italic">
                                {!! nl2br(e($post->user->signature)) !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Пагинация -->
    @if($posts->hasPages())
        <div class="mt-8">
            <div class="flex justify-center items-center gap-4 text-sm">
                {{ $posts->links() }}
            </div>
            <div class="text-center text-sm text-gray-600 mt-2">
                {{ __('main.showing') }} {{ $posts->firstItem() }} - {{ $posts->lastItem() }} {{ __('main.of') }} {{ $posts->total() }} {{ __('main.posts') }}
            </div>
        </div>
    @else
        <div class="text-center text-sm text-gray-600 mt-8">
            {{ __('main.total_posts_count') }}: {{ $posts->total() }}
        </div>
    @endif

    <!-- Форма ответа -->
    @auth
        @if(!$topic->is_locked || auth()->user()->can('moderate topics'))
            <div id="reply-form" class="bg-white rounded-lg shadow-lg mt-8 p-6">
                <h3 class="text-xl font-bold text-gray-700 mb-4">{{ __('main.reply_to_topic') }}</h3>
                
                <form method="POST" action="{{ route('posts.store', $topic) }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <textarea name="content" id="content" rows="6" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="{{ __('main.enter_your_reply') }}">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.attachments') }} ({{ __('main.optional') }})
                            </label>
                            <input type="file" name="attachments[]" id="attachments" multiple
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">{{ __('main.max_files') }}: 3, {{ __('main.max_size') }}: 2MB</p>
                            @error('attachments')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-colors">
                            {{ __('main.post_reply') }}
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800 mt-8 text-center">
                {{ __('main.topic_locked_no_replies') }}
            </div>
        @endif
    @endauth

    <!-- Кнопка возврата к категории -->
    <div class="mt-8 text-center">
        <a href="{{ route('categories.show', $topic->category) }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('main.back_to_category') }}
        </a>
    </div>
</div>

@push('modals')
<!-- Модальное окно жалобы -->
<div id="report-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">{{ __('main.report_content') }}</h3>
        
        <form id="report-form" method="POST" action="{{ route('reports.store') }}">
            @csrf
            <input type="hidden" name="reportable_type" id="reportable-type">
            <input type="hidden" name="reportable_id" id="reportable-id">
            
            <div class="mb-4">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.reason') }} *
                </label>
                <select name="reason" id="reason" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('main.select_reason') }}</option>
                    <option value="spam">{{ __('main.spam_advertising') }}</option>
                    <option value="offensive">{{ __('main.offensive_content') }}</option>
                    <option value="inappropriate">{{ __('main.inappropriate_content') }}</option>
                    <option value="other">{{ __('main.other') }}</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.comment') }} *
                </label>
                <textarea name="comment" id="comment" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="{{ __('main.describe_issue') }}"></textarea>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeReportModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    {{ __('main.cancel') }}
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    {{ __('main.send_report') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно для изображений -->
<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" onclick="hideImageModal()">
    <div class="max-w-4xl max-h-full p-4">
        <img id="modal-image" src="" alt="" class="max-w-full max-h-full object-contain">
        <div class="text-white text-center mt-4">
            <p id="modal-image-name"></p>
            <button onclick="hideImageModal()" class="mt-2 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                Закрыть
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
function openReportForm(type, id) {
    // Устанавливаем тип и ID контента для жалобы
    document.getElementById('reportable-type').value = type;
    document.getElementById('reportable-id').value = id;
    
    // Показываем модальное окно
    document.getElementById('report-modal').classList.remove('hidden');
    
    // Блокируем прокрутку на основной странице
    document.body.style.overflow = 'hidden';
}

function closeReportModal() {
    // Скрываем модальное окно
    document.getElementById('report-modal').classList.add('hidden');
    
    // Разблокируем прокрутку
    document.body.style.overflow = 'auto';
    
    // Сбрасываем форму
    document.getElementById('report-form').reset();
}

// Закрытие по клику вне модального окна
document.getElementById('report-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReportModal();
    }
});

// Закрытие по Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('report-modal').classList.contains('hidden')) {
        closeReportModal();
    }
});

// Модальное окно для изображений
function showImageModal(imageUrl, imageName) {
    document.getElementById('modal-image').src = imageUrl;
    document.getElementById('modal-image-name').textContent = imageName;
    document.getElementById('image-modal').classList.remove('hidden');
    document.getElementById('image-modal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function hideImageModal() {
    document.getElementById('image-modal').classList.add('hidden');
    document.getElementById('image-modal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Закрытие модального окна по ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('image-modal').classList.contains('hidden')) {
        hideImageModal();
    }
});
</script>
@endpush
@endsection