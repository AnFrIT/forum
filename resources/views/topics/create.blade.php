@extends('layouts.app')

@section('title', __('main.new_topic') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-700">{{ __('main.create_new_topic') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('main.in_category') }}: <strong>{{ $category->name }}</strong></p>
            @if($category->description)
                <p class="text-sm text-gray-500 mt-1">{{ $category->description }}</p>
            @endif
        </div>

        <form method="POST" action="{{ route('topics.store', $category) }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Заголовок темы -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.topic_title') }} *
                </label>
                <input type="text" name="title" id="title" required
                       value="{{ old('title') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                       placeholder="{{ __('main.enter_topic_title') }}">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Заголовок на арабском -->
            <div class="mb-6">
                <label for="title_ar" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.title_in_arabic') }} ({{ __('main.optional') }})
                </label>
                <input type="text" name="title_ar" id="title_ar"
                       value="{{ old('title_ar') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg arabic-text"
                       style="direction: rtl; text-align: right;"
                       placeholder="{{ __('main.enter_title_in_arabic') }}">
                @error('title_ar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Теги -->
            <div class="mb-6">
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.tags') }} ({{ __('main.comma_separated') }})
                </label>
                <input type="text" name="tags" id="tags"
                       value="{{ old('tags') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="{{ __('main.tags_example') }}">
                <p class="mt-1 text-sm text-gray-500">{{ __('main.tags_help') }}</p>
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Содержимое первого сообщения -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.first_message') }} *
                </label>
                <textarea name="content" id="content" rows="12" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="{{ __('main.describe_topic_in_detail') }}">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Содержимое на арабском -->
            <div class="mb-6">
                <label for="content_ar" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.message_in_arabic') }} ({{ __('main.optional') }})
                </label>
                <textarea name="content_ar" id="content_ar" rows="6"
                          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 arabic-text"
                          style="direction: rtl; text-align: right;"
                          placeholder="{{ __('main.enter_message_in_arabic') }}">{{ old('content_ar') }}</textarea>
                @error('content_ar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Вложения -->
            <div class="mb-6">
                <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.attachments') }}
                </label>
                <input type="file" name="attachments[]" id="attachments" multiple
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">{{ __('main.attachments_help') }}</p>
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Дополнительные опции для модераторов -->
            @if(auth()->user()->can('moderate topics'))
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-3">{{ __('main.moderator_options') }}</h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_pinned" value="1" 
                                   {{ old('is_pinned') ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">📌 {{ __('main.pin_topic') }}</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="is_locked" value="1"
                                   {{ old('is_locked') ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">🔒 {{ __('main.lock_topic') }}</span>
                        </label>
                    </div>
                </div>
            @endif

            <!-- Предварительный просмотр -->
            <div class="mb-6">
                <button type="button" onclick="togglePreview()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    👁️ {{ __('main.preview') }}
                </button>
                
                <div id="preview" class="hidden mt-4 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-2">{{ __('main.preview') }}:</h3>
                    <div id="preview-title" class="text-xl font-semibold text-blue-600 mb-2"></div>
                    <div id="preview-content" class="prose max-w-none"></div>
                    <div id="preview-content-ar" class="prose max-w-none arabic-text rtl mt-4 pt-4 border-t hidden"></div>
                </div>
            </div>

            <!-- Правила создания тем -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <h3 class="font-medium text-blue-800 mb-2">📋 {{ __('main.topic_creation_rules') }}</h3>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>{{ __('main.topic_rule_1') }}</li>
                    <li>{{ __('main.topic_rule_2') }}</li>
                    <li>{{ __('main.topic_rule_3') }}</li>
                    <li>{{ __('main.topic_rule_4') }}</li>
                    <li>{{ __('main.topic_rule_5') }}</li>
                </ul>
            </div>

            <!-- Кнопки -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('categories.show', $category) }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-center">
                    ❌ {{ __('main.cancel') }}
                </a>
                
                <button type="button" onclick="saveDraft()" 
                        class="px-6 py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    💾 {{ __('main.save_draft') }}
                </button>
                
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                    🚀 {{ __('main.create_topic') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Предварительный просмотр
function togglePreview() {
    const preview = document.getElementById('preview');
    const title = document.getElementById('title').value;
    const titleAr = document.getElementById('title_ar').value;
    const content = document.getElementById('content').value;
    const contentAr = document.getElementById('content_ar').value;
    
    if (preview.classList.contains('hidden')) {
        document.getElementById('preview-title').textContent = title || '{{ __('main.no_title') }}';
        document.getElementById('preview-content').innerHTML = content.replace(/\n/g, '<br>') || '{{ __('main.no_content') }}';
        
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

// Сохранение черновика
function saveDraft() {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    
    if (!title && !content) {
        alert('{{ __('main.title_or_content_required') }}');
        return;
    }
    
    // Сохраняем в localStorage
    const draft = {
        title: title,
        title_ar: document.getElementById('title_ar').value,
        tags: document.getElementById('tags').value,
        content: content,
        content_ar: document.getElementById('content_ar').value,
        category_id: '{{ $category->id }}',
        saved_at: new Date().toISOString()
    };
    
    localStorage.setItem('topic_draft', JSON.stringify(draft));
    
    // Показываем уведомление
    alert('{{ __('main.draft_saved') }}');
}

// Проверка наличия черновика при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const draftJson = localStorage.getItem('topic_draft');
    if (draftJson) {
        const draft = JSON.parse(draftJson);
        
        // Проверяем, что черновик для текущей категории
        if (draft.category_id === '{{ $category->id }}') {
            if (confirm('{{ __('main.found_draft_restore') }}')) {
                document.getElementById('title').value = draft.title || '';
                document.getElementById('title_ar').value = draft.title_ar || '';
                document.getElementById('tags').value = draft.tags || '';
                document.getElementById('content').value = draft.content || '';
                document.getElementById('content_ar').value = draft.content_ar || '';
            } else {
                // Удаляем черновик, если пользователь отказался восстанавливать
                localStorage.removeItem('topic_draft');
            }
        }
    }
});
</script>
@endpush
@endsection