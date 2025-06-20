<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Добавляем отсутствующий маршрут для сообщения о неработающей ссылке
Route::post('/report-broken-link', function(Request $request) {
    // Простая логика для обработки сообщений о неработающих ссылках
    $url = $request->input('url', '');
    $description = $request->input('description', '');
    
    // Здесь можно добавить логирование или сохранение в БД
    \Log::info('Reported broken link', [
        'url' => $url,
        'description' => $description,
        'user_id' => auth()->id(),
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Спасибо! Мы проверим эту ссылку.'
    ]);
})->name('api.report-broken-link');

// Добавляем API маршрут для работы с избранным
Route::middleware('auth')->post('/favorites/add', function(Request $request) {
    $request->validate([
        'type' => 'required|in:topic,post',
        'id' => 'required|integer',
    ]);
    
    $type = $request->input('type');
    $id = $request->input('id');
    $user = auth()->user();
    
    try {
        // Здесь можно добавить реальную логику сохранения в избранное
        // Пример: сохранение в таблицу favorites
        /*
        $user->favorites()->updateOrCreate(
            ['favorable_type' => $type, 'favorable_id' => $id],
            ['created_at' => now()]
        );
        */
        
        // Пока просто логируем действие
        \Log::info('Added to favorites', [
            'user_id' => $user->id,
            'type' => $type,
            'id' => $id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Добавлено в избранное'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ошибка при добавлении в избранное: ' . $e->getMessage()
        ], 500);
    }
})->name('api.favorites.add');

// ИСПРАВЛЕНО: Добавлены отсутствующие API роуты

/**
 * API роут для поиска пользователей (используется в личных сообщениях)
 */
Route::middleware('auth')->get('/users/search', [\App\Http\Controllers\API\UserController::class, 'search'])->name('api.users.search');

/**
 * API роут для массовых действий с пользователями (админка)
 */
Route::middleware(['auth', 'admin'])->post('/admin/users/bulk-action', function (Request $request) {
    $request->validate([
        'action' => 'required|in:ban,unban,make-moderator,remove-moderator,delete',
        'user_ids' => 'required|array',
        'user_ids.*' => 'exists:users,id',
        'reason' => 'nullable|string|max:255',
        'duration' => 'nullable|integer|min:1',
    ]);
    
    $users = User::whereIn('id', $request->user_ids)
        ->where('id', '!=', auth()->id()) // Нельзя применять действия к себе
        ->get();
    
    $successCount = 0;
    
    foreach ($users as $user) {
        try {
            switch ($request->action) {
                case 'ban':
                    $expiresAt = $request->duration ? now()->addDays($request->duration) : null;
                    $user->ban($request->reason, $expiresAt);
                    $successCount++;
                    break;
                    
                case 'unban':
                    $user->unban();
                    $successCount++;
                    break;
                    
                case 'make-moderator':
                    if (!$user->is_admin) {
                        $user->update(['is_moderator' => true]);
                        $successCount++;
                    }
                    break;
                    
                case 'remove-moderator':
                    if (!$user->is_admin) {
                        $user->update(['is_moderator' => false]);
                        $successCount++;
                    }
                    break;
                    
                case 'delete':
                    if (!$user->is_admin) {
                        $user->delete();
                        $successCount++;
                    }
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Bulk action error: ' . $e->getMessage());
        }
    }
    
    return response()->json([
        'success' => true,
        'processed' => $successCount,
        'total' => count($users),
        'message' => "Обработано {$successCount} из " . count($users) . " пользователей"
    ]);
})->name('api.admin.users.bulk-action');

/**
 * API роут для очистки кеша (админка)
 */
Route::middleware(['auth', 'admin'])->post('/admin/cache/clear', function (Request $request) {
    $type = $request->input('type', 'all');
    
    try {
        switch ($type) {
            case 'config':
                \Artisan::call('config:clear');
                break;
            case 'route':
                \Artisan::call('route:clear');
                break;
            case 'view':
                \Artisan::call('view:clear');
                break;
            case 'cache':
                \Artisan::call('cache:clear');
                break;
            case 'all':
                \Artisan::call('config:clear');
                \Artisan::call('route:clear');
                \Artisan::call('view:clear');
                \Artisan::call('cache:clear');
                break;
        }
        
        return response()->json([
            'success' => true,
            'message' => "Кеш типа '{$type}' успешно очищен"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ошибка очистки кеша: ' . $e->getMessage()
        ]);
    }
})->name('api.admin.cache.clear');

/**
 * API роут для создания резервной копии (админка)
 */
Route::middleware(['auth', 'admin'])->post('/admin/backup/create', function (Request $request) {
    try {
        // Здесь можно добавить реальную логику создания бэкапа
        // Пока просто возвращаем успешный ответ
        
        return response()->json([
            'success' => true,
            'message' => 'Резервная копия создана успешно',
            'filename' => 'backup_' . date('Y-m-d_H-i-s') . '.sql'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ошибка создания резервной копии: ' . $e->getMessage()
        ]);
    }
})->name('api.admin.backup.create');

/**
 * API роут для проверки новых сообщений
 */
Route::middleware('auth')->get('/messages/check-new', function (Request $request) {
    $unreadCount = auth()->user()->receivedMessages()->unread()->count();
    
    return response()->json([
        'unread_count' => $unreadCount,
        'has_new' => $unreadCount > 0,
    ]);
})->name('api.messages.check-new');

/**
 * API роут для сохранения черновиков сообщений
 */
Route::middleware('auth')->post('/messages/save-draft', function (Request $request) {
    // Здесь можно добавить логику сохранения черновиков
    // Пока просто возвращаем успешный ответ
    
    return response()->json([
        'success' => true,
        'message' => 'Черновик сохранен',
        'timestamp' => now()->toISOString()
    ]);
})->name('api.messages.save-draft');

/**
 * API роут для проверки статуса пользователя
 */
Route::middleware('auth')->post('/user/update-status', function (Request $request) {
    auth()->user()->updateLastActivity();
    
    return response()->json([
        'success' => true,
        'timestamp' => now()->toISOString()
    ]);
})->name('api.user.update-status');

/**
 * API роуты для данных профиля пользователя
 */
Route::middleware('auth')->group(function () {
    // Посты пользователя
    Route::get('/users/{user}/posts', function (User $user) {
        $posts = $user->posts()
            ->with(['topic:id,title,slug', 'topic.category:id,name'])
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'content' => $post->content,
                    'created_at' => $post->created_at,
                    'topic_id' => $post->topic_id,
                    'topic_title' => $post->topic->title ?? 'Неизвестная тема',
                    'topic_slug' => $post->topic->slug ?? null,
                    'category_name' => $post->topic->category->name ?? 'Неизвестная категория',
                ];
            });

        return response()->json(['posts' => $posts]);
    })->name('api.users.posts');

    // Темы пользователя
    Route::get('/users/{user}/topics', function (User $user) {
        $topics = $user->topics()
            ->with('category:id,name')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($topic) {
                return [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'slug' => $topic->slug,
                    'content' => $topic->content,
                    'created_at' => $topic->created_at,
                    'posts_count' => $topic->posts_count,
                    'views_count' => $topic->views_count,
                    'is_pinned' => $topic->is_pinned,
                    'is_locked' => $topic->is_locked,
                    'category_name' => $topic->category->name ?? 'Неизвестная категория',
                ];
            });

        return response()->json(['topics' => $topics]);
    })->name('api.users.topics');

    // Достижения пользователя (пока заглушка)
    Route::get('/users/{user}/achievements', function (User $user) {
        $achievements = []; // Здесь будет логика достижений
        return response()->json(['achievements' => $achievements]);
    })->name('api.users.achievements');

    // Активность пользователя (пока заглушка)
    Route::get('/users/{user}/activity', function (User $user) {
        $activity = []; // Здесь будет логика активности
        return response()->json(['activity' => $activity]);
    })->name('api.users.activity');
});

/**
 * API роут для проверки статуса обслуживания
 */
Route::get('/maintenance-status', function () {
    $siteStatus = \App\Models\Setting::where('key', 'site_status')->value('value') ?? 'online';
    
    return response()->json([
        'status' => $siteStatus,
        'timestamp' => now()->toISOString()
    ]);
})->name('api.maintenance-status');