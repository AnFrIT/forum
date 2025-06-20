@extends('layouts.app')

@section('title', 'Редактировать сообщение')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-700">Редактировать сообщение</h1>
            <p class="text-gray-600 mt-2">в теме: <strong>{{ $post->topic->title }}</strong></p>
            <p class="text-sm text-gray-500 mt-1">
                Сообщение от {{ $post->created_at->format('d.m.Y H:i') }}
                @if($post->created_at != $post->updated_at)
                    (последнее изменение: {{ $post->updated_at->format('d.m.Y H:i') }})
                @endif
            </p>
        </div>

        <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Содержимое сообщения -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Содержимое сообщения *
                </label>
                <textarea name="content" id="content" rows="12" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Введите содержимое сообщения...">{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Содержимое на арабском -->
            <div class="mb-6">
                <label for="content_ar" class="block text-sm font-medium text-gray-700 mb-2">
                    Сообщение на арабском (необязательно)
                </label>
                <textarea name="content_ar" id="content_ar" rows="6"
                          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 arabic-text"
                          style="direction: rtl; text-align: right;"
                          placeholder="اكتب رسالتك هنا...">{{ old('content_ar', $post->content_ar) }}</textarea>
                @error('content_ar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Существующие вложения -->
            @if($post->attachments && $post->attachments->count() > 0)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Текущие вложения
                    </label>
                    <div class="space-y-2 p-4 bg-gray-50 border border-gray-200 rounded-md">
                        @foreach($post->attachments as $attachment)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-gray-600">📎</span>
                                    <span class="ml-2 text-sm">{{ $attachment->name }}</span>
                                    <span class="ml-2 text-xs text-gray-500">({{ $attachment->size_formatted }})</span>
                                </div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="remove_attachments[]" value="{{ $attachment->id }}"
                                           class="mr-2 text-red-600 focus:ring-red-500">
                                    <span class="text-sm text-red-600">Удалить</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Новые вложения -->
            <div class="mb-6">
                <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                    Добавить новые вложения
                </label>
                <input type="file" name="attachments[]" id="attachments" multiple
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">Максимум 5 файлов, до 10MB каждый</p>
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Причина редактирования -->
            <div class="mb-6">
                <label for="edit_reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Причина редактирования (будет видна всем пользователям)
                </label>
                <input type="text" name="edit_reason" id="edit_reason"
                       value="{{ old('edit_reason') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Исправление ошибки, добавление информации и т.д.">
                @error('edit_reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- История изменений -->
            @if($post->edit_history && count($post->edit_history) > 0)
                <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-3">📋 История изменений</h3>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        @foreach($post->edit_history as $edit)
                            <div class="text-sm">
                                <span class="text-gray-500">{{ \Carbon\Carbon::parse($edit['date'])->format('d.m.Y H:i') }}</span>
                                @if($edit['user'])
                                    <span class="text-blue-600">{{ $edit['user'] }}</span>
                                @endif
                                @if($edit['reason'])
                                    <span class="text-gray-700">- {{ $edit['reason'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Оригинальное сообщение для сравнения -->
            <div class="mb-6">
                <button type="button" onclick="toggleOriginal()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    📋 Показать оригинал для сравнения
                </button>
                
                <div id="original-content" class="hidden mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <h3 class="font-medium text-yellow-800 mb-2">Оригинальное сообщение:</h3>
                    <div class="prose max-w-none text-sm text-gray-700">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                    @if($post->content_ar)
                        <div class="mt-4 pt-4 border-t prose max-w-none text-sm text-gray-700 arabic-text rtl">
                            {!! nl2br(e($post->content_ar)) !!}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Предварительный просмотр -->
            <div class="mb-6">
                <button type="button" onclick="togglePreview()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    👁️ Предварительный просмотр
                </button>
                
                <div id="preview" class="hidden mt-4 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-2">Предварительный просмотр:</h3>
                    <div id="preview-content" class="prose max-w-none"></div>
                    <div id="preview-content-ar" class="prose max-w-none arabic-text rtl mt-4 pt-4 border-t hidden"></div>
                </div>
            </div>

            <!-- Настройки модератора -->
            @if(auth()->user()->can('moderate posts'))
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-3">Опции модератора</h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="silent_edit" value="1" 
                                   {{ old('silent_edit') ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Тихое редактирование (не показывать факт изменения)</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="lock_further_edits" value="1"
                                   {{ old('lock_further_edits') ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Заблокировать дальнейшее редактирование автором</span>
                        </label>
                    </div>
                </div>
            @endif

            <!-- Правила редактирования -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <h3 class="font-medium text-blue-800 mb-2">📋 Правила редактирования</h3>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>Редактирование должно улучшать качество сообщения</li>
                    <li>Указывайте причину редактирования для прозрачности</li>
                    <li>Не изменяйте смысл сообщения кардинально</li>
                    <li>История изменений доступна всем пользователям</li>
                    <li>Модераторы могут отклонить неуместные изменения</li>
                </ul>
            </div>

            <!-- Кнопки -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('topics.show', $post->topic) }}#post-{{ $post->id }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-center">
                    ❌ Отменить
                </a>
                
                @can('delete', $post)
                    <button type="button" onclick="deletePost()" 
                            class="px-6 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        🗑️ Удалить сообщение
                    </button>
                @endcan
                
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                    💾 Сохранить изменения
                </button>
            </div>
        </form>
    </div>

    <!-- Форма удаления (скрытая) -->
    @can('delete', $post)
        <form id="delete-form" method="POST" action="{{ route('posts.destroy', $post) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endcan
</div>

@push('scripts')
<script>
// Предварительный просмотр
function togglePreview() {
    const preview = document.getElementById('preview');
    const content = document.getElementById('content').value;
    const contentAr = document.getElementById('content_ar').value;
    
    if (preview.classList.contains('hidden')) {
        document.getElementById('preview-content').innerHTML = content.replace(/\n/g, '<br>') || 'Нет содержимого';
        
        const previewContentAr = document.getElementById('preview-content-ar');
        if (contentAr) {
            previewContentAr.innerHTML = contentAr.replace(/\n/g, '<br>');
            previewContentAr.classList.remove('hidden');
        } else {
            previewContentAr.classList.add('hidden');
        }
        
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
}

// Показать оригинал
function toggleOriginal() {
    const original = document.getElementById('original-content');
    if (original.classList.contains('hidden')) {
        original.classList.remove('hidden');
    } else {
        original.classList.add('hidden');
    }
}

// Удаление сообщения
function deletePost() {
    if (confirm('Вы уверены, что хотите удалить это сообщение? Это действие нельзя отменить.')) {
        document.getElementById('delete-form').submit();
    }
}

// Автосохранение в localStorage
function saveDraft() {
    const draft = {
        content: document.getElementById('content').value,
        content_ar: document.getElementById('content_ar').value,
        edit_reason: document.getElementById('edit_reason').value,
        timestamp: new Date().getTime()
    };
    
    localStorage.setItem('forum_post_edit_{{ $post->id }}', JSON.stringify(draft));
}

// Загрузка черновика при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const draftKey = 'forum_post_edit_{{ $post->id }}';
    const savedDraft = localStorage.getItem(draftKey);
    
    if (savedDraft) {
        const draft = JSON.parse(savedDraft);
        const now = new Date().getTime();
        const draftAge = now - draft.timestamp;
        
        // Если черновик не старше 1 часа
        if (draftAge < 60 * 60 * 1000) {
            if (confirm('Найден сохраненный черновик изменений. Загрузить его?')) {
                if (draft.content && draft.content !== document.getElementById('content').value) {
                    document.getElementById('content').value = draft.content;
                }
                if (draft.content_ar && draft.content_ar !== document.getElementById('content_ar').value) {
                    document.getElementById('content_ar').value = draft.content_ar;
                }
                document.getElementById('edit_reason').value = draft.edit_reason || '';
            }
        } else {
            localStorage.removeItem(draftKey);
        }
    }
});

// Автосохранение каждые 30 секунд
setInterval(saveDraft, 30000);

// Очистка черновика при отправке формы
document.querySelector('form').addEventListener('submit', function() {
    localStorage.removeItem('forum_post_edit_{{ $post->id }}');
});

// Предупреждение о несохраненных изменениях
const originalContent = document.getElementById('content').value;
const originalContentAr = document.getElementById('content_ar').value;

window.addEventListener('beforeunload', function(e) {
    const currentContent = document.getElementById('content').value;
    const currentContentAr = document.getElementById('content_ar').value;
    
    if (currentContent !== originalContent || currentContentAr !== originalContentAr) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Проверка на пустое содержимое
document.getElementById('content').addEventListener('input', function() {
    const submitBtn = document.querySelector('button[type="submit"]');
    if (this.value.trim().length === 0) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
});
</script>
@endpush
@endsection