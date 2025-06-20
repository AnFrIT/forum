@extends('layouts.app')

@section('title', __('main.manage_categories'))

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6 mt-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-700">📁 {{ __('main.manage_categories') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('main.create_organize_forum') }}</p>
            </div>
            
            <div class="mt-4 lg:mt-0 flex flex-wrap gap-2">
                <a href="{{ route('admin.categories.create') }}" 
                   class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    ➕ {{ __('main.create_category') }}
                </a>
                <button onclick="saveOrder()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    💾 {{ __('main.save_order') }}
                </button>
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    ← {{ __('main.back') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Инструкции -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="font-medium text-blue-800 mb-2">💡 {{ __('main.how_to_work_categories') }}</h3>
        <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
            <li>{{ __('main.drag_categories_order') }}</li>
            <li>{{ __('main.use_subcategories_organization') }}</li>
            <li>{{ __('main.assign_moderators_category') }}</li>
            <li>{{ __('main.dont_forget_save_order') }}</li>
        </ul>
    </div>

    <!-- Структура категорий -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">{{ __('main.categories_structure') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('main.drag_change_order') }}</p>
        </div>

        @if($categories->count() > 0)
            <div id="categories-container" class="divide-y divide-gray-200">
                @foreach($categories as $category)
                    <div class="category-item p-6" data-id="{{ $category->id }}">
                        <div class="flex items-center justify-between">
                            <!-- Информация о категории -->
                            <div class="flex items-center flex-grow">
                                <div class="drag-handle cursor-move mr-4 text-gray-400 hover:text-gray-600">
                                    ⋮⋮
                                </div>
                                
                                <div class="flex-grow">
                                    <div class="flex items-center mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800 {{ $category->is_rtl ? 'arabic-text' : '' }}">
                                            {{ $category->name }}
                                            @if($category->name_ar)
                                                <span class="text-sm text-gray-500 arabic-text">({{ $category->name_ar }})</span>
                                            @endif
                                        </h3>
                                        
                                        @if($category->is_private)
                                            <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">🔒 {{ __('main.private') }}</span>
                                        @endif
                                        
                                        @if(!$category->is_active)
                                            <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">⏸️ {{ __('main.disabled') }}</span>
                                        @endif
                                    </div>
                                    
                                    @if($category->description)
                                        <p class="text-gray-600 text-sm mb-2 {{ $category->is_rtl ? 'arabic-text' : '' }}">
                                            {{ $category->description }}
                                            @if($category->description_ar)
                                                <span class="text-gray-500 arabic-text">({{ $category->description_ar }})</span>
                                            @endif
                                        </p>
                                    @endif
                                    
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                        <span>📝 {{ $category->topics_count ?? $category->topics->count() }} {{ __('main.topics') }}</span>
                                        <span>💬 {{ $category->posts_count ?? 0 }} {{ __('main.messages') }}</span>
                                        <span>📅 {{ $category->created_at->format('d.m.Y') }}</span>
                                        
                                        @if($category->moderators && $category->moderators->count() > 0)
                                            <span>
                                                ��️ {{ __('main.moderators') }}: 
                                                @foreach($category->moderators as $moderator)
                                                    <span class="text-blue-600">{{ $moderator->name }}</span>@if(!$loop->last), @endif
                                                @endforeach
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Действия -->
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('categories.show', $category) }}" 
                                   class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded" 
                                   title="{{ __('main.view') }}">
                                    👁️
                                </a>
                                
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded" 
                                   title="{{ __('main.edit') }}">
                                    ✏️
                                </a>
                                
                                @if($category->children && $category->children->count() > 0)
                                    <span class="p-2 text-gray-400" title="{{ __('main.has_subcategories') }}">
                                        📁
                                    </span>
                                @else
                                    <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}" 
                                       class="p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded" 
                                       title="{{ __('main.add_subcategory') }}">
                                        ➕
                                    </a>
                                @endif
                                
                                <button data-category-id="{{ $category->id }}" class="delete-category-btn p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded" title="{{ __('main.delete') }}">
                                    🗑️
                                </button>
                            </div>
                        </div>

                        <!-- Подкатегории -->
                        @if($category->children && $category->children->count() > 0)
                            <div class="mt-4 ml-8 space-y-3">
                                @foreach($category->children as $subcategory)
                                    <div class="subcategory-item border border-gray-200 rounded-lg p-4 bg-gray-50" data-id="{{ $subcategory->id }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center flex-grow">
                                                <div class="drag-handle cursor-move mr-4 text-gray-400 hover:text-gray-600">
                                                    ⋮⋮
                                                </div>
                                                
                                                <div class="flex-grow">
                                                    <div class="flex items-center mb-1">
                                                        <h4 class="font-medium text-gray-700 {{ $subcategory->is_rtl ? 'arabic-text' : '' }}">
                                                            {{ $subcategory->name }}
                                                            @if($subcategory->name_ar)
                                                                <span class="text-sm text-gray-500 arabic-text">({{ $subcategory->name_ar }})</span>
                                                            @endif
                                                        </h4>
                                                        
                                                        @if($subcategory->is_private)
                                                            <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">🔒</span>
                                                        @endif
                                                        
                                                        @if(!$subcategory->is_active)
                                                            <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">⏸️</span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($subcategory->description)
                                                        <p class="text-gray-500 text-sm mb-2 {{ $subcategory->is_rtl ? 'arabic-text' : '' }}">
                                                            {{ $subcategory->description }}
                                                        </p>
                                                    @endif
                                                    
                                                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                                                        <span>📝 {{ $subcategory->topics_count ?? $subcategory->topics->count() }} {{ __('main.topics') }}</span>
                                                        <span>💬 {{ $subcategory->posts_count ?? 0 }} {{ __('main.messages') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-1 ml-4">
                                                <a href="{{ route('categories.show', $subcategory) }}" 
                                                   class="p-1 text-blue-600 hover:text-blue-800 text-sm" 
                                                   title="{{ __('main.view') }}">
                                                    👁️
                                                </a>
                                                
                                                <a href="{{ route('admin.categories.edit', $subcategory) }}" 
                                                   class="p-1 text-green-600 hover:text-green-800 text-sm" 
                                                   title="{{ __('main.edit') }}">
                                                    ✏️
                                                </a>
                                                
                                                <button data-category-id="{{ $subcategory->id }}" class="delete-category-btn p-1 text-red-600 hover:text-red-800 text-sm" title="{{ __('main.delete') }}">
                                                    🗑️
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- Пустое состояние -->
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">📁</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('main.no_categories') }}</h3>
                <p class="text-gray-600 mb-6">
                    {{ __('main.create_first_category_forum') }}
                </p>
                <a href="{{ route('admin.categories.create') }}" 
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    ➕ {{ __('main.create_first_category') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Статистика -->
    @if($categories->count() > 0)
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $categories->count() }}</div>
                <div class="text-sm text-gray-600">{{ __('main.main_categories') }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-green-600">{{ $categories->sum(function($cat) { return $cat->children ? $cat->children->count() : 0; }) }}</div>
                <div class="text-sm text-gray-600">{{ __('main.subcategories') }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $categories->sum('topics_count') }}</div>
                <div class="text-sm text-gray-600">{{ __('main.total_topics') }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $categories->sum('posts_count') }}</div>
                <div class="text-sm text-gray-600">{{ __('main.total_posts') }}</div>
            </div>
        </div>
    @endif
</div>

<!-- Форма удаления (скрытая) -->
<form id="delete-form" method="POST" action="{{ route('admin.categories.destroy.submit') }}" style="display: none;">
    @csrf
    <input type="hidden" name="category_id" id="category_id_to_delete">
</form>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Инициализация сортировки для основных категорий
const categoriesContainer = document.getElementById('categories-container');
if (categoriesContainer) {
    new Sortable(categoriesContainer, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onEnd: function (evt) {
            console.log('Moved category from', evt.oldIndex, 'to', evt.newIndex);
        }
    });
}

// Инициализация сортировки для подкатегорий
document.querySelectorAll('.category-item').forEach(categoryItem => {
    const subcategoriesContainer = categoryItem.querySelector('.space-y-3');
    if (subcategoriesContainer) {
        new Sortable(subcategoriesContainer, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function (evt) {
                console.log('Moved subcategory from', evt.oldIndex, 'to', evt.newIndex);
            }
        });
    }
});

// Добавляем обработчик для кнопок удаления категорий
document.addEventListener('DOMContentLoaded', function() {
    // Находим все кнопки удаления и добавляем обработчик
    document.querySelectorAll('.delete-category-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.getAttribute('data-category-id');
            deleteCategory(categoryId);
        });
    });
});

// Сохранение порядка
function saveOrder() {
    const categories = [];
    
    document.querySelectorAll('.category-item').forEach((item, index) => {
        const categoryId = item.dataset.id;
        const subcategories = [];
        
        item.querySelectorAll('.subcategory-item').forEach((subItem, subIndex) => {
            subcategories.push({
                id: subItem.dataset.id,
                order: subIndex
            });
        });
        
        categories.push({
            id: categoryId,
            order: index,
            subcategories: subcategories
        });
    });
    
    fetch('{{ route("admin.categories.reorder") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ categories: categories })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('{{ __("main.order_saved") }}');
        } else {
            alert('{{ __("main.error_saving_order") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("main.error_saving_order") }}');
    });
}

// Удаление категории
function deleteCategory(categoryId) {
    if (confirm('{{ __("main.confirm_delete_category") }}')) {
        document.getElementById('category_id_to_delete').value = categoryId;
        document.getElementById('delete-form').submit();
    }
}

// Стили для сортировки
const style = document.createElement('style');
style.textContent = `
    .sortable-ghost {
        opacity: 0.5;
        background: #f3f4f6;
    }
    .sortable-chosen {
        background: #dbeafe;
    }
    .sortable-drag {
        background: white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .drag-handle:hover {
        cursor: move;
    }
`;
document.head.appendChild(style);

// Автосохранение при перетаскивании (с задержкой)
let saveTimeout;
function scheduleSave() {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveOrder, 2000); // Сохранить через 2 секунды после последнего перемещения
}

// Добавить обработчики событий для автосохранения
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('#categories-container, .space-y-3');
    containers.forEach(container => {
        if (container.sortable) {
            container.sortable.option('onEnd', function(evt) {
                scheduleSave();
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.arabic-text {
    font-family: 'Amiri', serif;
    direction: rtl;
    text-align: right;
}
</style>
@endpush
@endsection