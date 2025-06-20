<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $categories = Category::with(['parent', 'children', 'moderators'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::main()->where('is_active', true)->get();
        $availableModerators = User::where('is_moderator', true)
            ->orWhere('is_admin', true)
            ->where('is_banned', false)
            ->get();

        return view('admin.categories.create', compact('parentCategories', 'availableModerators'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_private' => 'boolean',
            'is_readonly' => 'boolean',
            'requires_approval' => 'boolean',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|in:blue,green,red,yellow,purple,orange',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
            'moderators' => 'nullable|array',
            'moderators.*' => 'exists:users,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // Генерируем slug, если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Обеспечиваем уникальность slug
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Category::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        // Обработка загруженного изображения
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }

        // Преобразуем checkbox значения
        $validated['is_active'] = $request->has('is_active');
        $validated['is_private'] = $request->has('is_private');
        $validated['is_readonly'] = $request->has('is_readonly');
        $validated['requires_approval'] = $request->has('requires_approval');

        // Удаляем moderators из основных данных
        $moderators = $validated['moderators'] ?? [];
        unset($validated['moderators']);

        $category = Category::create($validated);

        // Сохраняем модераторов
        if (!empty($moderators)) {
            $category->moderators()->sync($moderators);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Категория успешно создана!');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::main()
            ->where('id', '!=', $category->id)
            ->where('is_active', true)
            ->get();
        
        $availableModerators = User::where('is_moderator', true)
            ->orWhere('is_admin', true)
            ->where('is_banned', false)
            ->get();

        $categories = Category::where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'categories', 'availableModerators'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_private' => 'boolean',
            'is_readonly' => 'boolean',
            'requires_approval' => 'boolean',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|in:blue,green,red,yellow,purple,orange',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
            'moderators' => 'nullable|array',
            'moderators.*' => 'exists:users,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // Удаляем текущее изображение, если требуется
        if ($request->has('remove_image') && $category->image) {
            Storage::disk('public')->delete($category->image);
            $validated['image'] = null;
        }

        // Обработка нового изображения
        if ($request->hasFile('image')) {
            // Удаляем старое изображение
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            
            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }

        // Генерируем новый slug, если изменилось название
        if ($validated['name'] !== $category->name && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Обеспечиваем уникальность slug
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Category::where('slug', $validated['slug'])->where('id', '!=', $category->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        // Преобразуем checkbox значения
        $validated['is_active'] = $request->has('is_active');
        $validated['is_private'] = $request->has('is_private');
        $validated['is_readonly'] = $request->has('is_readonly');
        $validated['requires_approval'] = $request->has('requires_approval');

        // Удаляем moderators из основных данных
        $moderators = $validated['moderators'] ?? [];
        unset($validated['moderators']);

        $category->update($validated);

        // Сохраняем модераторов
        $category->moderators()->sync($moderators);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Категория успешно обновлена!');
    }

    public function destroy(Category $category)
    {
        // Проверка на наличие подкатегорий
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Невозможно удалить категорию с подкатегориями! Сначала удалите подкатегории.');
        }

        // Проверка на наличие тем
        if ($category->topics()->exists()) {
            return back()->with('error', 'Невозможно удалить категорию с темами! Сначала переместите или удалите темы.');
        }

        try {
            // Удаляем изображение категории, если оно есть
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            // Отсоединяем модераторов
            $category->moderators()->detach();
            
            // Удаляем категорию
            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Категория успешно удалена!');
        } catch (\Exception $e) {
            \Log::error('Ошибка при удалении категории: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при удалении категории: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        foreach ($request->categories as $order => $categoryId) {
            Category::where('id', $categoryId)->update(['order' => $order]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * ИСПРАВЛЕНО: Добавлен метод для показа формы
     */
    public function show(Category $category)
    {
        $stats = [
            'topics_count' => $category->topics()->count(),
            'posts_count' => $category->posts_count,
            'subscribers_count' => 0, // Если есть подписки
        ];

        $recentTopics = $category->topics()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.categories.show', compact('category', 'stats', 'recentTopics'));
    }
    
    /**
     * Метод для тестирования предпросмотра
     */
    public function testPreview()
    {
        return view('admin.categories.test-preview');
    }

    /**
     * Метод для обработки POST-запроса на удаление категории
     */
    public function destroySubmit(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $category = Category::findOrFail($request->category_id);

        // Проверка на наличие подкатегорий
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Невозможно удалить категорию с подкатегориями! Сначала удалите подкатегории.');
        }

        // Проверка на наличие тем
        if ($category->topics()->exists()) {
            return back()->with('error', 'Невозможно удалить категорию с темами! Сначала переместите или удалите темы.');
        }

        try {
            // Удаляем изображение категории, если оно есть
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            // Отсоединяем модераторов
            $category->moderators()->detach();
            
            // Удаляем категорию
            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Категория успешно удалена!');
        } catch (\Exception $e) {
            \Log::error('Ошибка при удалении категории: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при удалении категории: ' . $e->getMessage());
        }
    }
}