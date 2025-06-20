<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\SearchQuery;
use App\Models\Setting;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Получаем настройки форума
        $siteName = Setting::where('key', 'site_name')->value('value') ?? config('app.name');
        $siteDescription = Setting::where('key', 'site_description')->value('value') ?? 'Добро пожаловать в форум AL-INSAF - место для конструктивного диалога и обсуждений';

        // Получаем категории с подкатегориями
        $categories = Category::with(['children', 'topics'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Получаем популярные категории
        $popularCategories = Category::getPopular(6);

        // Статистика
        $stats = [
            'topics' => Topic::count(),
            'posts' => Post::count(),
            'users' => User::count(),
            'categories' => Category::count(),
        ];

        // Последние темы
        $recentTopics = Topic::with(['user', 'category'])
            ->latest()
            ->limit(5)
            ->get();

        return view('home', compact('categories', 'stats', 'recentTopics', 'siteName', 'siteDescription', 'popularCategories'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $author = $request->get('author', '');
        $categoryId = $request->get('category', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $searchIn = $request->get('search_in', ['topics', 'posts']);
        $sort = $request->get('sort', 'relevance');
        
        // Основные данные для страницы поиска
        $categories = Category::whereNull('parent_id')->where('is_active', true)->with('children')->get();
        $totalTopics = Topic::count();
        $totalPosts = Post::count();
        $recentTopics = Topic::with(['user', 'category'])->latest()->limit(5)->get();
        
        // Получаем популярные запросы для отображения
        $popularQueries = SearchQuery::getPopular(10);
        
        // Счетчик поисковых запросов
        $totalSearches = 0;
        try {
            $totalSearches = SearchQuery::sum('count') ?? 0;
        } catch (\Exception $e) {
            // Игнорируем ошибку, если таблица не существует
        }
        
        $results = collect();
        
        if ($query) {
            // Логируем поисковый запрос
            $filters = [
                'author' => $author,
                'category' => $categoryId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search_in' => $searchIn
            ];
            
            $userId = auth()->id();
            SearchQuery::log($query, $userId, $filters);
            
            // Подготавливаем запрос для поиска
            $searchQuery = strtolower($query);
            
            // Поиск по темам
            if (in_array('topics', $searchIn)) {
                $topicsQuery = Topic::with(['user', 'category'])
                    ->where(function($q) use ($searchQuery) {
                        $q->whereRaw('LOWER(title) LIKE ?', ['%' . $searchQuery . '%'])
                          ->orWhereRaw('LOWER(content) LIKE ?', ['%' . $searchQuery . '%']);
                    });
                
                // Фильтр по автору
                if ($author) {
                    $authorSearch = strtolower($author);
                    $topicsQuery->whereHas('user', function($q) use ($authorSearch) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . $authorSearch . '%']);
                    });
                }
                
                // Фильтр по категории
                if ($categoryId) {
                    $topicsQuery->where('category_id', $categoryId);
                }
                
                // Фильтр по дате
                if ($dateFrom) {
                    $topicsQuery->whereDate('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $topicsQuery->whereDate('created_at', '<=', $dateTo);
                }
                
                $topics = $topicsQuery->get();
                $results = $results->merge($topics);
            }
            
            // Поиск по постам
            if (in_array('posts', $searchIn)) {
                $postsQuery = Post::with(['user', 'topic.category'])
                    ->whereRaw('LOWER(content) LIKE ?', ['%' . $searchQuery . '%']);
                
                // Фильтр по автору
                if ($author) {
                    $authorSearch = strtolower($author);
                    $postsQuery->whereHas('user', function($q) use ($authorSearch) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . $authorSearch . '%']);
                    });
                }
                
                // Фильтр по категории через тему
                if ($categoryId) {
                    $postsQuery->whereHas('topic', function($q) use ($categoryId) {
                        $q->where('category_id', $categoryId);
                    });
                }
                
                // Фильтр по дате
                if ($dateFrom) {
                    $postsQuery->whereDate('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $postsQuery->whereDate('created_at', '<=', $dateTo);
                }
                
                $posts = $postsQuery->get();
                $results = $results->merge($posts);
            }
            
            // Сортировка результатов
            switch ($sort) {
                case 'date':
                    $results = $results->sortByDesc('created_at');
                    break;
                case 'author':
                    $results = $results->sortBy('user.name');
                    break;
                case 'relevance':
                default:
                    // Простая сортировка по релевантности
                    $results = $results->map(function($item) use ($searchQuery) {
                        $content = strtolower($item->content ?? '');
                        $title = strtolower($item->title ?? $item->topic->title ?? '');
                        $relevance = substr_count($title . ' ' . $content, $searchQuery);
                        $item->relevance = min($relevance * 25, 100);
                        return $item;
                    })->sortByDesc('relevance');
                    break;
            }
            
            // Пагинация
            $perPage = 20;
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;
            $items = $results->slice($offset, $perPage);
            
            // Создаем объект для пагинации
            $results = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $results->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Пустая коллекция если нет запроса
            $results = collect();
        }
        
        // Проверяем, является ли запрос AJAX
        if ($request->ajax()) {
            return response()->json([
                'results' => $results,
                'total' => $results->total() ?? 0
            ]);
        }
        
        return view('search', compact(
            'query', 
            'results', 
            'categories',
            'totalTopics',
            'totalPosts', 
            'recentTopics',
            'popularQueries',
            'totalSearches'
        ));
    }
}