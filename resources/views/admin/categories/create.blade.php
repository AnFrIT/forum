@extends('layouts.app')

@section('title', (isset($category) ? __('main.edit_category') : __('main.create_category')) . ' - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between mt-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-1">
                {{ isset($category) ? '✏️ ' . __('main.edit_category') : '➕ ' . __('main.create_new_category') }}
            </h1>
            <p class="text-gray-600">
                {{ isset($category) ? __('main.editing_category') . ': <strong>' . $category->name . '</strong>' : __('main.creating_new_category_desc') }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('main.back_to_categories') }}
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-2 bg-gradient-to-r from-blue-500 to-purple-500"></div>
        
        <form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @if(isset($category))
                @method('PUT')
            @endif

            <div class="space-y-8">
                <!-- Основная информация -->
                <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-700 p-2 rounded-full mr-2">📝</span>
                        {{ __('main.basic_information') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Название категории -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.category_name') }} *
                            </label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', isset($category) ? $category->name : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="{{ __('main.enter_category_name') }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Название на арабском -->
                        <div>
                            <label for="name_ar" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.name_in_arabic') }}
                            </label>
                            <input type="text" name="name_ar" id="name_ar"
                                   value="{{ old('name_ar', isset($category) ? $category->name_ar : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 arabic-text"
                                   style="direction: rtl; text-align: right;"
                                   placeholder="اسم الفئة">
                            @error('name_ar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                URL (slug)
                            </label>
                            <input type="text" name="slug" id="slug"
                                   value="{{ old('slug', isset($category) ? $category->slug : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="auto-generated-from-name">
                            <p class="mt-1 text-xs text-gray-500">{{ __('main.leave_empty_auto_generate') }}</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Родительская категория -->
                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.parent_category') }}
                            </label>
                            <select name="parent_id" id="parent_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('main.main_category') }}</option>
                                @if(isset($parentCategories))
                                    @foreach($parentCategories as $parentCategory)
                                        <option value="{{ $parentCategory->id }}" 
                                                {{ old('parent_id', isset($category) ? $category->parent_id : request('parent')) == $parentCategory->id ? 'selected' : '' }}>
                                            {{ $parentCategory->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('parent_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ИСПРАВЛЕНО: Добавлено поле Order -->
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                Порядок сортировки *
                            </label>
                            <input type="number" name="order" id="order" required min="0"
                                   value="{{ old('order', isset($category) ? $category->order : 0) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0">
                            <p class="mt-1 text-xs text-gray-500">Чем меньше число, тем выше в списке</p>
                            @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Описание -->
                <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-green-100 text-green-700 p-2 rounded-full mr-2">📄</span>
                        {{ __('main.description') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Описание на русском -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.category_description') }}
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="{{ __('main.category_description_placeholder') }}">{{ old('description', isset($category) ? $category->description : '') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Описание на арабском -->
                        <div>
                            <label for="description_ar" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.description_in_arabic') }}
                            </label>
                            <textarea name="description_ar" id="description_ar" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 arabic-text"
                                      style="direction: rtl; text-align: right;"
                                      placeholder="وصف مختصر لما يتم مناقشته في هذه الفئة">{{ old('description_ar', isset($category) ? $category->description_ar : '') }}</textarea>
                            @error('description_ar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Иконка и изображение -->
                <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-purple-100 text-purple-700 p-2 rounded-full mr-2">🎨</span>
                        {{ __('main.visual_design') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Иконка -->
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.icon_emoji_or_css') }}
                            </label>
                            <input type="text" name="icon" id="icon"
                                   value="{{ old('icon', isset($category) ? $category->icon : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="📁 или fa-folder">
                            <p class="mt-1 text-xs text-gray-500">{{ __('main.emoji_or_css_class') }}</p>
                            @error('icon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Цвет -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.color_scheme') }}
                            </label>
                            <select name="color" id="color"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="blue" {{ old('color', isset($category) ? $category->color : 'blue') === 'blue' ? 'selected' : '' }}>{{ __('main.blue') }}</option>
                                <option value="green" {{ old('color', isset($category) ? $category->color : '') === 'green' ? 'selected' : '' }}>{{ __('main.green') }}</option>
                                <option value="red" {{ old('color', isset($category) ? $category->color : '') === 'red' ? 'selected' : '' }}>{{ __('main.red') }}</option>
                                <option value="yellow" {{ old('color', isset($category) ? $category->color : '') === 'yellow' ? 'selected' : '' }}>{{ __('main.yellow') }}</option>
                                <option value="purple" {{ old('color', isset($category) ? $category->color : '') === 'purple' ? 'selected' : '' }}>{{ __('main.purple') }}</option>
                                <option value="orange" {{ old('color', isset($category) ? $category->color : '') === 'orange' ? 'selected' : '' }}>{{ __('main.orange') }}</option>
                            </select>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Изображение -->
                        <div class="md:col-span-2">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.category_image') }}
                            </label>
                            <input type="file" name="image" id="image" accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">{{ __('main.image_requirements') }}</p>
                            
                            @if(isset($category) && !empty($category->image))
                                <div class="mt-2">
                                    <img src="{{ Storage::url($category->image) }}" alt="{{ __('main.current_image') }}" class="w-16 h-16 object-cover rounded">
                                    <label class="flex items-center mt-2">
                                        <input type="checkbox" name="remove_image" value="1" class="mr-2">
                                        <span class="text-sm text-red-600">{{ __('main.remove_current_image') }}</span>
                                    </label>
                                </div>
                            @endif
                            
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Настройки доступа -->
                <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-red-100 text-red-700 p-2 rounded-full mr-2">🔒</span>
                        {{ __('main.access_settings') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Статус активности -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', isset($category) ? $category->is_active : true) ? 'checked' : '' }}
                                           class="mr-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="text-sm font-medium text-gray-700">{{ __('main.category_active') }}</span>
                                </label>
                                <p class="ml-6 text-xs text-gray-500">{{ __('main.inactive_categories_hidden') }}</p>
                            </div>

                            <!-- Приватность -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_private" value="1" 
                                           {{ old('is_private', isset($category) ? $category->is_private : false) ? 'checked' : '' }}
                                           class="mr-3 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    <span class="text-sm font-medium text-gray-700">{{ __('main.private_category') }}</span>
                                </label>
                                <p class="ml-6 text-xs text-gray-500">{{ __('main.private_category_desc') }}</p>
                            </div>

                            <!-- Только для чтения -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_readonly" value="1" 
                                           {{ old('is_readonly', isset($category) ? $category->is_readonly : false) ? 'checked' : '' }}
                                           class="mr-3 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                    <span class="text-sm font-medium text-gray-700">{{ __('main.read_only') }}</span>
                                </label>
                                <p class="ml-6 text-xs text-gray-500">{{ __('main.read_only_desc') }}</p>
                            </div>

                            <!-- Требует модерации -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requires_approval" value="1" 
                                           {{ old('requires_approval', isset($category) ? $category->requires_approval : false) ? 'checked' : '' }}
                                           class="mr-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <span class="text-sm font-medium text-gray-700">{{ __('main.requires_moderation') }}</span>
                                </label>
                                <p class="ml-6 text-xs text-gray-500">{{ __('main.requires_moderation_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Модераторы -->
                <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-yellow-100 text-yellow-700 p-2 rounded-full mr-2">🛡️</span>
                        {{ __('main.category_moderators') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="moderators" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('main.assign_moderators') }}
                            </label>
                            
                            <div class="relative">
                                <div class="flex items-center border border-gray-300 rounded-md bg-white">
                                    <div class="flex-grow">
                                        <input type="text" id="moderator-search" 
                                               class="w-full px-4 py-3 border-0 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="{{ __('main.search_moderators') }}">
                                    </div>
                                    <div class="px-3 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <div class="mt-2 border border-gray-300 rounded-md bg-white max-h-64 overflow-y-auto">
                                    <select name="moderators[]" id="moderators" multiple
                                            class="w-full min-h-[100px] px-0 py-0 border-0 focus:outline-none focus:ring-0">
                                        @if(isset($availableModerators))
                                            @foreach($availableModerators as $moderator)
                                                @php
                                                    $selectedModerators = old('moderators', isset($category) && method_exists($category, 'moderators') ? $category->moderators->pluck('id')->toArray() : []);
                                                @endphp
                                                <option value="{{ $moderator->id }}" 
                                                        {{ in_array($moderator->id, $selectedModerators) ? 'selected' : '' }}
                                                        class="px-4 py-2 hover:bg-blue-50 cursor-pointer">
                                                    <span class="font-medium">{{ $moderator->name }}</span> ({{ $moderator->email }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-3 flex flex-wrap gap-2" id="selected-moderators-display">
                                <!-- Здесь будут отображаться выбранные модераторы -->
                            </div>
                            
                            <p class="mt-2 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('main.hold_ctrl_multiple_select') }}
                            </p>
                            
                            @error('moderators')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO настройки -->
                <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-indigo-100 text-indigo-700 p-2 rounded-full mr-2">🔍</span>
                        {{ __('main.seo_settings') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Title
                            </label>
                            <input type="text" name="meta_title" id="meta_title"
                                   value="{{ old('meta_title', isset($category) ? $category->meta_title : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="{{ __('main.seo_title_placeholder') }}">
                            @error('meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Description
                            </label>
                            <textarea name="meta_description" id="meta_description" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="{{ __('main.seo_description_placeholder') }}">{{ old('meta_description', isset($category) ? $category->meta_description : '') }}</textarea>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Предварительный просмотр -->
                <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-cyan-500"></div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-cyan-100 text-cyan-700 p-2 rounded-full mr-2">👁️</span>
                        {{ __('main.preview') }}
                    </h3>
                    
                    <div id="preview" class="bg-white p-4 rounded border">
                        <div class="flex items-center">
                            <div id="preview-icon" class="text-2xl mr-3">📁</div>
                            <div class="flex-grow">
                                <h4 id="preview-name" class="text-lg font-semibold text-blue-600">{{ __('main.category_name') }}</h4>
                                <p id="preview-description" class="text-gray-600 text-sm">{{ __('main.category_description') }}</p>
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

                <!-- Кнопки -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end pt-6 mt-6 border-t">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-center flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ __('main.cancel') }}
                    </a>
                    
                    @if(isset($category))
                        <button type="button" onclick="deleteCategory()" 
                                class="px-6 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            {{ __('main.delete_category') }}
                        </button>
                    @endif
                    
                    <button type="submit" 
                            class="px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            @if(isset($category))
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            @endif
                        </svg>
                        {{ isset($category) ? __('main.save_changes') : __('main.create_category') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Форма удаления (скрытая) -->
@if(isset($category))
    <form id="delete-form" method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif

@push('scripts')
<script>
// Автогенерация slug из названия
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slugField = document.getElementById('slug');
    
    if (!slugField.value || slugField.dataset.auto !== 'false') {
        const slug = name.toLowerCase()
            .replace(/[^\w\s-]/g, '') // Убираем специальные символы
            .replace(/[\s_-]+/g, '-') // Заменяем пробелы на дефисы
            .replace(/^-+|-+$/g, ''); // Убираем дефисы в начале и конце
        
        slugField.value = slug;
        slugField.dataset.auto = 'true';
    }
    
    updatePreview();
});

// Отключение автогенерации при ручном редактировании slug
document.getElementById('slug').addEventListener('input', function() {
    this.dataset.auto = 'false';
});

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

// Удаление категории
function deleteCategory() {
    if (confirm('{{ __("main.delete_category_confirm") }}')) {
        if (confirm('{{ __("main.action_irreversible_continue") }}')) {
            document.getElementById('delete-form').submit();
        }
    }
}

// Улучшенный мультиселект для модераторов
document.addEventListener('DOMContentLoaded', function() {
    const moderatorsSelect = document.getElementById('moderators');
    const moderatorSearch = document.getElementById('moderator-search');
    const selectedModeratorsDisplay = document.getElementById('selected-moderators-display');
    
    if (moderatorsSelect && moderatorSearch) {
        // Функция для обновления визуального отображения выбранных модераторов
        function updateSelectedModerators() {
            selectedModeratorsDisplay.innerHTML = '';
            const selectedOptions = Array.from(moderatorsSelect.selectedOptions);
            
            if (selectedOptions.length === 0) {
                selectedModeratorsDisplay.innerHTML = '<span class="text-gray-500 italic">{{ __("main.no_moderators_selected") }}</span>';
                return;
            }
            
            selectedOptions.forEach(option => {
                const moderatorTag = document.createElement('div');
                moderatorTag.className = 'bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-sm flex items-center';
                moderatorTag.innerHTML = `
                    <span class="mr-1">👤</span>
                    ${option.textContent}
                    <button type="button" class="ml-2 text-blue-700 hover:text-blue-900" data-id="${option.value}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                
                // Обработчик для удаления модератора
                moderatorTag.querySelector('button').addEventListener('click', function() {
                    const moderatorId = this.getAttribute('data-id');
                    Array.from(moderatorsSelect.options).forEach(opt => {
                        if (opt.value === moderatorId) {
                            opt.selected = false;
                        }
                    });
                    updateSelectedModerators();
                });
                
                selectedModeratorsDisplay.appendChild(moderatorTag);
            });
        }
        
        // Инициализация отображения
        updateSelectedModerators();
        
        // Фильтр модераторов при вводе в поле поиска
        moderatorSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            Array.from(moderatorsSelect.options).forEach(option => {
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(query) ? '' : 'none';
            });
        });
        
        // Обновление отображения при изменении выбора
        moderatorsSelect.addEventListener('change', updateSelectedModerators);
    }
});

// Предупреждение о несохраненных изменениях
let originalFormData = new FormData(document.querySelector('form'));

window.addEventListener('beforeunload', function(e) {
    const currentFormData = new FormData(document.querySelector('form'));
    let hasChanges = false;
    
    for (let [key, value] of currentFormData.entries()) {
        if (originalFormData.get(key) !== value) {
            hasChanges = true;
            break;
        }
    }
    
    if (hasChanges) {
        e.preventDefault();
        e.returnValue = '{{ __("main.unsaved_changes_warning") }}';
    }
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