@extends('layouts.app')

@section('title', 'Test Category Preview - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-700">
                Test Category Preview
            </h1>
            <p class="text-gray-600 mt-2">This page is for testing the category preview functionality</p>
        </div>

        <div class="space-y-6">
            <!-- Test Form -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">📝 Test Inputs</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.category_name') }} *
                        </label>
                        <input type="text" name="name" id="name"
                               value="Test Category"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="{{ __('main.enter_category_name') }}">
                    </div>

                    <!-- Arabic Name -->
                    <div>
                        <label for="name_ar" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.name_in_arabic') }}
                        </label>
                        <input type="text" name="name_ar" id="name_ar"
                               value="اختبار القسم"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 arabic-text"
                               style="direction: rtl; text-align: right;"
                               placeholder="اسم الفئة">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.category_description') }}
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="{{ __('main.category_description_placeholder') }}">This is a test category description for preview testing</textarea>
                    </div>

                    <!-- Arabic Description -->
                    <div>
                        <label for="description_ar" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.description_in_arabic') }}
                        </label>
                        <textarea name="description_ar" id="description_ar" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 arabic-text"
                                  style="direction: rtl; text-align: right;"
                                  placeholder="وصف القسم بالعربية">هذا وصف قسم اختباري للتحقق من وظيفة المعاينة</textarea>
                    </div>

                    <!-- Icon -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.icon_emoji_or_css') }}
                        </label>
                        <input type="text" name="icon" id="icon"
                               value="📊"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="📁 или fa-folder">
                    </div>

                    <!-- Color -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.color_scheme') }}
                        </label>
                        <select name="color" id="color"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="blue">{{ __('main.blue') }}</option>
                            <option value="green">{{ __('main.green') }}</option>
                            <option value="red">{{ __('main.red') }}</option>
                            <option value="yellow">{{ __('main.yellow') }}</option>
                            <option value="purple">{{ __('main.purple') }}</option>
                            <option value="orange">{{ __('main.orange') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">👁️ {{ __('main.preview') }}</h3>
                
                <div id="preview" class="bg-white p-4 rounded border">
                    <div class="flex items-center">
                        <div id="preview-icon" class="text-2xl mr-3">📊</div>
                        <div class="flex-grow">
                            <h4 id="preview-name" class="text-lg font-semibold text-blue-600">Test Category</h4>
                            <p id="preview-description" class="text-gray-600 text-sm">This is a test category description for preview testing</p>
                        </div>
                        <div class="text-sm text-gray-500">
                            <div>{{ __('main.topics') }}: 0</div>
                            <div>{{ __('main.posts') }}: 0</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-sm text-gray-600">
                    <p class="mb-2"><strong>{{ __('main.note') }}:</strong> {{ __('main.preview_shows') }}</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>{{ __('main.preview_category_name') }}</li>
                        <li>{{ __('main.preview_description') }}</li>
                        <li>{{ __('main.preview_icon_and_color') }}</li>
                        <li>{{ __('main.preview_arabic_text') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Обновление предварительного просмотра
function updatePreview() {
    const name = document.getElementById('name').value || '{{ __("main.category_name") }}';
    const name_ar = document.getElementById('name_ar').value;
    const description = document.getElementById('description').value || '{{ __("main.category_description") }}';
    const description_ar = document.getElementById('description_ar').value;
    const icon = document.getElementById('icon').value || '📁';
    const color = document.getElementById('color').value || 'blue';
    
    // Update the preview elements
    document.getElementById('preview-name').textContent = name;
    document.getElementById('preview-description').textContent = description;
    document.getElementById('preview-icon').textContent = icon;
    
    // Apply color class
    const previewElement = document.getElementById('preview');
    previewElement.className = previewElement.className.replace(/bg-\w+-\d+/g, '');
    previewElement.classList.add(`bg-${color}-50`);
    
    // Display Arabic name if available
    if (name_ar && name_ar.trim() !== '') {
        const nameArElement = document.getElementById('preview-name-ar');
        if (!nameArElement) {
            const newNameArElement = document.createElement('span');
            newNameArElement.id = 'preview-name-ar';
            newNameArElement.className = 'block text-sm text-gray-600 arabic-text mt-1';
            newNameArElement.style.direction = 'rtl';
            newNameArElement.style.textAlign = 'right';
            document.getElementById('preview-name').after(newNameArElement);
        }
        document.getElementById('preview-name-ar').textContent = name_ar;
    } else {
        const nameArElement = document.getElementById('preview-name-ar');
        if (nameArElement) nameArElement.remove();
    }
    
    // Display Arabic description if available
    if (description_ar && description_ar.trim() !== '') {
        const descArElement = document.getElementById('preview-description-ar');
        if (!descArElement) {
            const newDescArElement = document.createElement('span');
            newDescArElement.id = 'preview-description-ar';
            newDescArElement.className = 'block text-xs text-gray-500 arabic-text mt-1';
            newDescArElement.style.direction = 'rtl';
            newDescArElement.style.textAlign = 'right';
            document.getElementById('preview-description').after(newDescArElement);
        }
        document.getElementById('preview-description-ar').textContent = description_ar;
    } else {
        const descArElement = document.getElementById('preview-description-ar');
        if (descArElement) descArElement.remove();
    }
}

// Обработчики для всех полей предварительного просмотра
['name', 'name_ar', 'description', 'description_ar', 'icon', 'color'].forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', updatePreview);
        field.addEventListener('change', updatePreview);
    }
});

// Инициализация предварительного просмотра при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
});
</script>
@endpush

@push('styles')
<style>
.arabic-text {
    font-family: 'Amiri', serif;
}
</style>
@endpush
@endsection 