@extends('layouts.app')

@section('title', __('main.search') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-700 mb-6">Поиск по форуму</h1>
        
        <form method="GET" action="{{ route('search') }}" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow">
                    <input type="text" name="q" 
                           value="{{ request('q') }}"
                           placeholder="Введите ключевые слова для поиска..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
                </div>
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                    🔍 Найти
                </button>
            </div>
            
            <!-- Расширенные параметры поиска -->
            <div class="border-t pt-4">
                <button type="button" onclick="toggleAdvanced()" 
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ⚙️ Расширенный поиск
                </button>
                
                <div id="advanced-search" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Автор:</label>
                        <input type="text" name="author" id="author" 
                               value="{{ request('author') }}"
                               placeholder="Имя пользователя"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Категория:</label>
                        <select name="category" id="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Все категории</option>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @if($category->subcategories)
                                        @foreach($category->subcategories as $subcategory)
                                            <option value="{{ $subcategory->id }}" {{ request('category') == $subcategory->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;&nbsp;&nbsp;{{ $subcategory->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Дата с:</label>
                        <input type="date" name="date_from" id="date_from" 
                               value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Дата до:</label>
                        <input type="date" name="date_to" id="date_to" 
                               value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Тип контента:</label>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="search_in[]" value="topics" 
                                       {{ in_array('topics', request('search_in', ['topics', 'posts'])) ? 'checked' : '' }}
                                       class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">Темы</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="search_in[]" value="posts" 
                                       {{ in_array('posts', request('search_in', ['topics', 'posts'])) ? 'checked' : '' }}
                                       class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">Сообщения</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if(request('q'))
        <!-- Результаты поиска -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-700">
                    Результаты поиска для: "<span class="text-blue-600">{{ request('q') }}</span>"
                </h2>
                @if(isset($results))
                    <span class="text-sm text-gray-500">
                        Найдено: {{ $results->total() }} результатов
                    </span>
                @endif
            </div>

            @if(isset($results) && $results->count() > 0)
                <!-- Фильтры результатов -->
                <div class="mb-6 border-b pb-4">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'relevance']) }}" 
                           class="px-3 py-1 text-sm rounded-full {{ request('sort', 'relevance') === 'relevance' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            По релевантности
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'date']) }}" 
                           class="px-3 py-1 text-sm rounded-full {{ request('sort') === 'date' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            По дате
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'author']) }}" 
                           class="px-3 py-1 text-sm rounded-full {{ request('sort') === 'author' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            По автору
                        </a>
                    </div>
                </div>

                <!-- Список результатов -->
                <div class="space-y-6">
                    @foreach($results as $result)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-grow">
                                    <!-- Тип результата -->
                                    <div class="flex items-center gap-2 mb-2">
                                        @if($result->getTable() === 'topics')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">📝 Тема</span>
                                        @else
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">💬 Сообщение</span>
                                        @endif
                                        
                                        @if($result->category)
                                            <span class="text-xs text-gray-500">
                                                в <a href="{{ route('categories.show', $result->category) }}" class="text-blue-500 hover:underline">{{ $result->category->name }}</a>
                                            </span>
                                        @elseif($result->topic && $result->topic->category)
                                            <span class="text-xs text-gray-500">
                                                в <a href="{{ route('categories.show', $result->topic->category) }}" class="text-blue-500 hover:underline">{{ $result->topic->category->name }}</a>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Заголовок -->
                                    <h3 class="text-lg font-semibold mb-2">
                                        @if($result->getTable() === 'topics')
                                            <a href="{{ route('topics.show', $result) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                @if($result->is_pinned) 📌 @endif
                                                @if($result->is_locked) 🔒 @endif
                                                {!! highlightSearchTerms($result->title, request('q')) !!}
                                            </a>
                                        @else
                                            <a href="{{ route('topics.show', $result->topic) }}#post-{{ $result->id }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                Re: {!! highlightSearchTerms($result->topic->title, request('q')) !!}
                                            </a>
                                        @endif
                                    </h3>

                                    <!-- Превью контента -->
                                    <div class="text-gray-700 mb-3">
                                        <p>{!! highlightSearchTerms(Str::limit(strip_tags($result->content), 200), request('q')) !!}</p>
                                    </div>

                                    <!-- Метаданные -->
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                        <span>
                                            👤 <a href="{{ route('profile.show', $result->user) }}" class="text-blue-500 hover:underline">{{ $result->user->name }}</a>
                                        </span>
                                        <span>📅 {{ $result->created_at->format('d.m.Y H:i') }}</span>
                                        
                                        @if($result->getTable() === 'topics')
                                            <span>💬 {{ $result->posts_count ?? $result->posts->count() }} ответов</span>
                                            <span>👀 {{ $result->views_count ?? 0 }} просмотров</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Релевантность -->
                                <div class="ml-4 text-right">
                                    <div class="text-xs text-gray-400">Релевантность</div>
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $result->relevance ?? 50 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Пагинация -->
                @if($results->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $results->appends(request()->query())->links() }}
                    </div>
                @endif

            @elseif(isset($results))
                <!-- Нет результатов -->
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Ничего не найдено</h3>
                    <p class="text-gray-600 mb-6">
                        По вашему запросу "<span class="font-medium">{{ request('q') }}</span>" ничего не найдено.
                    </p>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-left max-w-md mx-auto">
                        <h4 class="font-medium text-blue-800 mb-2">💡 Советы по поиску:</h4>
                        <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                            <li>Проверьте правильность написания</li>
                            <li>Используйте более общие термины</li>
                            <li>Попробуйте синонимы</li>
                            <li>Уберите лишние фильтры</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <!-- Популярные запросы -->
        @if(isset($popularQueries) && $popularQueries->count() > 0)
            <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">🔥 Популярные запросы</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($popularQueries as $query)
                        <a href="{{ route('search', ['q' => $query->term]) }}" 
                           class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                            {{ $query->term }} <span class="text-gray-500">({{ $query->count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <!-- Стартовая страница поиска -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🔍</div>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Поиск по форуму</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Найдите интересующие вас темы и сообщения. Используйте расширенный поиск для более точных результатов.
                </p>
            </div>

            <!-- Статистика поиска -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $totalTopics ?? 0 }}</div>
                    <div class="text-sm text-blue-700">Всего тем</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $totalPosts ?? 0 }}</div>
                    <div class="text-sm text-green-700">Всего сообщений</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $totalSearches ?? 0 }}</div>
                    <div class="text-sm text-purple-700">Поисковых запросов</div>
                </div>
            </div>

            <!-- Недавняя активность -->
            @if(isset($recentTopics) && $recentTopics->count() > 0)
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">📈 Недавняя активность</h3>
                    <div class="space-y-3">
                        @foreach($recentTopics->take(5) as $topic)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <a href="{{ route('topics.show', $topic) }}" 
                                       class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ Str::limit($topic->title, 60) }}
                                    </a>
                                    <div class="text-sm text-gray-500">
                                        в <a href="{{ route('categories.show', $topic->category) }}" class="text-blue-500 hover:underline">{{ $topic->category->name }}</a>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $topic->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleAdvanced() {
    const advanced = document.getElementById('advanced-search');
    if (advanced) {
        if (advanced.classList.contains('hidden')) {
            advanced.classList.remove('hidden');
            advanced.classList.add('animate-fade-in');
        } else {
            advanced.classList.add('hidden');
            advanced.classList.remove('animate-fade-in');
        }
    }
}

// Автозаполнение поиска
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        searchInput.focus();
        
        // Подсветка результатов поиска
        if (searchInput.value) {
            document.getElementById('advanced-search')?.classList.remove('hidden');
        }
    }
});

// Сохранение поисковых запросов
const searchForm = document.querySelector('form');
if (searchForm) {
    searchForm.addEventListener('submit', function() {
        const query = this.querySelector('input[name="q"]').value.trim();
        if (query) {
            // Сохраняем в localStorage для истории поиска
            let searches = JSON.parse(localStorage.getItem('forum_searches') || '[]');
            searches.unshift(query);
            searches = [...new Set(searches)]; // Убираем дубликаты
            searches = searches.slice(0, 10); // Максимум 10 последних запросов
            localStorage.setItem('forum_searches', JSON.stringify(searches));
        }
    });
}
</script>
@endpush


@endsection