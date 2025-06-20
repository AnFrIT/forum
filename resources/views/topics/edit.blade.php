@extends('layouts.app')

@section('title', __('main.edit_topic') . ': ' . $topic->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-700">Редактировать тему</h1>
            <div class="text-gray-600 mt-2">
                В категории: 
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

        <form method="POST" action="{{ route('topics.update', $topic) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Заголовок темы -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Заголовок темы *
                </label>
                <input type="text" name="title" id="title" required
                       value="{{ old('title', $topic->title) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                       placeholder="Введите заголовок темы...">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Заголовок на арабском -->
            <div class="mb-6">
                <label for="title_ar" class="block text-sm font-medium text-gray-700 mb-2">
                    Заголовок на арабском (необязательно)
                </label>
                <input type="text" name="title_ar" id="title_ar"
                       value="{{ old('title_ar', $topic->title_ar) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg arabic-text"
                       style="direction: rtl; text-align: right;"
                       placeholder="اكتب عنوان الموضوع هنا...">
                @error('title_ar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Теги -->
            <div class="mb-6">
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                    Теги (через запятую)
                </label>
                <input type="text" name="tags" id="tags"
                       value="{{ old('tags', $topic->tags ? $topic->tags->pluck('name')->implode(', ') : '') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="например: обсуждение, вопрос, помощь">
                <p class="mt-1 text-sm text-gray-500">Теги помогают другим пользователям находить ваши темы</p>
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Дополнительные опции для модераторов -->
            @if(auth()->user()->can('moderate topics'))
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-3">Опции модератора</h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_pinned" value="1" 
                                   {{ old('is_pinned', $topic->is_pinned) ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">📌 Закрепить тему</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="is_locked" value="1"
                                   {{ old('is_locked', $topic->is_locked) ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">🔒 Заблокировать тему для ответов</span>
                        </label>
                    </div>
                </div>
            @endif

            <!-- Смена категории для модераторов -->
            @if(auth()->user()->can('moderate topics'))
                <div class="mb-6">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Переместить в категорию
                    </label>
                    <select name="category_id" id="category_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ old('category_id', $topic->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @if($category->subcategories)
                                @foreach($category->subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}" 
                                            {{ old('category_id', $topic->category_id) == $subcategory->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $subcategory->name }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

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
            @if($topic->edit_history && count($topic->edit_history) > 0)
                <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-3">📋 История изменений</h3>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        @foreach($topic->edit_history as $edit)
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

            <!-- Предварительный просмотр -->
            <div class="mb-6">
                <button type="button" onclick="togglePreview()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    👁️ Предварительный просмотр
                </button>
                
                <div id="preview" class="hidden mt-4 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-2">Предварительный просмотр:</h3>
                    <div id="preview-title" class="text-xl font-semibold text-blue-600 mb-2"></div>
                    <div id="preview-title-ar" class="text-lg font-medium text-gray-600 arabic-text rtl mb-2 hidden"></div>
                    <div id="preview-tags" class="mb-4"></div>
                </div>
            </div>

            <!-- Правила редактирования -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <h3 class="font-medium text-blue-800 mb-2">📋 Правила редактирования</h3>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>Редактирование должно улучшать качество темы</li>
                    <li>Указывайте причину редактирования для прозрачности</li>
                    <li>Не изменяйте смысл темы кардинально</li>
                    <li>Уважайте мнение других участников</li>
                </ul>
            </div>

            <!-- Кнопки -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('topics.show', $topic) }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-center">
                    ❌ Отменить
                </a>
                
                @can('delete', $topic)
                    <button type="button" onclick="deleteTopic()" 
                            class="px-6 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        🗑️ Удалить тему
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
    @can('delete', $topic)
        <form id="delete-form" method="POST" action="{{ route('topics.destroy', $topic) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endcan

    @push('scripts')
    <script>
    // Предварительный просмотр
    function togglePreview() {
        const preview = document.getElementById('preview');
        const title = document.getElementById('title').value;
        const titleAr = document.getElementById('title_ar').value;
        const tags = document.getElementById('tags').value;
        
        if (preview.classList.contains('hidden')) {
            document.getElementById('preview-title').textContent = title || 'Без заголовка';
            
            const previewTitleAr = document.getElementById('preview-title-ar');
            if (titleAr) {
                previewTitleAr.textContent = titleAr;
                previewTitleAr.classList.remove('hidden');
            } else {
                previewTitleAr.classList.add('hidden');
            }
            
            const previewTags = document.getElementById('preview-tags');
            if (tags) {
                const tagArray = tags.split(',').map(tag => tag.trim()).filter(tag => tag);
                previewTags.innerHTML = tagArray.map(tag => 
                    `<span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded mr-2">${tag}</span>`
                ).join('');
            } else {
                previewTags.innerHTML = '';
            }
            
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }

    // Удаление темы
    function deleteTopic() {
        if (confirm('Вы уверены, что хотите удалить эту тему? Это действие нельзя отменить.')) {
            if (confirm('Все сообщения в теме будут также удалены. Продолжить?')) {
                document.getElementById('delete-form').submit();
            }
        }
    }

    // Автосохранение в localStorage
    function saveDraft() {
        const draft = {
            title: document.getElementById('title').value,
            title_ar: document.getElementById('title_ar').value,
            tags: document.getElementById('tags').value,
            edit_reason: document.getElementById('edit_reason').value,
            timestamp: new Date().getTime()
        };
        
        localStorage.setItem('forum_topic_edit_{{ $topic->id }}', JSON.stringify(draft));
    }

    // Загрузка черновика при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        const draftKey = 'forum_topic_edit_{{ $topic->id }}';
        const savedDraft = localStorage.getItem(draftKey);
        
        if (savedDraft) {
            const draft = JSON.parse(savedDraft);
            const now = new Date().getTime();
            const draftAge = now - draft.timestamp;
            
            // Если черновик не старше 1 часа
            if (draftAge < 60 * 60 * 1000) {
                if (confirm('Найден сохраненный черновик изменений. Загрузить его?')) {
                    if (draft.title !== document.getElementById('title').value) {
                        document.getElementById('title').value = draft.title || '';
                    }
                    if (draft.title_ar !== document.getElementById('title_ar').value) {
                        document.getElementById('title_ar').value = draft.title_ar || '';
                    }
                    if (draft.tags !== document.getElementById('tags').value) {
                        document.getElementById('tags').value = draft.tags || '';
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
        localStorage.removeItem('forum_topic_edit_{{ $topic->id }}');
    });

    // Предупреждение о несохраненных изменениях
    const originalValues = {
        title: document.getElementById('title').value,
        title_ar: document.getElementById('title_ar').value,
        tags: document.getElementById('tags').value
    };

    window.addEventListener('beforeunload', function(e) {
        const currentValues = {
            title: document.getElementById('title').value,
            title_ar: document.getElementById('title_ar').value,
            tags: document.getElementById('tags').value
        };
        
        const hasChanges = Object.keys(originalValues).some(key => 
            originalValues[key] !== currentValues[key]
        );
        
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    </script>
    @endpush
</div>
@endsection