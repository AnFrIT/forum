<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PrivateMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

// Language switching routes
Route::get('/language/{language}', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/api/language/current', [LanguageController::class, 'current'])->name('language.current');

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Terms and Privacy routes
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

// Category routes
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Topic routes
Route::get('/category/{category}/create', [TopicController::class, 'create'])->name('topics.create');
Route::post('/category/{category}/topics', [TopicController::class, 'store'])->name('topics.store');
Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
Route::get('/topics/{topic}/edit', [TopicController::class, 'edit'])->name('topics.edit');
Route::put('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');
Route::post('/topics/{topic}/lock', [TopicController::class, 'lock'])->name('topics.lock');
Route::post('/topics/{topic}/pin', [TopicController::class, 'pin'])->name('topics.pin');

// Post routes
Route::post('/topics/{topic}/posts', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

// Profile routes
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard route for authenticated users
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');
});

// Private messages
Route::middleware('auth')->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [PrivateMessageController::class, 'index'])->name('index');
    Route::post('/', [PrivateMessageController::class, 'store'])->name('store');
    Route::get('/{message}', [PrivateMessageController::class, 'show'])->name('show');
    Route::delete('/{message}', [PrivateMessageController::class, 'destroy'])->name('destroy');
    Route::post('/mark-all-read', [PrivateMessageController::class, 'markAllRead'])->name('mark-all-read');
    Route::post('/{message}/mark-read', [PrivateMessageController::class, 'markRead'])->name('mark-read');
    Route::post('/{message}/archive', [PrivateMessageController::class, 'archive'])->name('archive');
    Route::post('/bulk-action', [PrivateMessageController::class, 'bulkAction'])->name('bulk-action');
    Route::get('/api/check-new', [PrivateMessageController::class, 'checkNew'])->name('check-new');
    Route::post('/save-draft', [PrivateMessageController::class, 'saveDraft'])->name('save-draft');
    Route::post('/{message}/track-reading-time', [PrivateMessageController::class, 'trackReadingTime'])->name('track-reading-time');
    Route::post('/{conversation}/reply', [PrivateMessageController::class, 'replyToConversation'])->name('reply');
    Route::get('/start-with/{user}', [PrivateMessageController::class, 'startConversationWith'])->name('start-with');
});

// Reports
Route::middleware('auth')->group(function () {
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Categories management
    Route::resource('categories', AdminCategoryController::class);
    Route::post('/categories/reorder', [AdminCategoryController::class, 'reorder'])->name('categories.reorder');
    Route::post('/categories/destroy', [AdminCategoryController::class, 'destroySubmit'])->name('categories.destroy.submit');
    Route::get('/categories/test-preview', [AdminCategoryController::class, 'testPreview'])->name('categories.test-preview');

    // Users management
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
    Route::post('/users/{user}/add-moderator', [AdminUserController::class, 'addModerator'])->name('users.add-moderator');
    Route::post('/users/{user}/remove-moderator', [AdminUserController::class, 'removeModerator'])->name('users.remove-moderator');
    
    // ИСПРАВЛЕНО: Добавлен роут для массовых действий с пользователями
    Route::post('/users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');

    // Settings management
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/create', [AdminSettingController::class, 'create'])->name('settings.create');
    Route::post('/settings', [AdminSettingController::class, 'store'])->name('settings.store');
    Route::delete('/settings/{key}', [AdminSettingController::class, 'destroy'])->name('settings.destroy');

    // ИСПРАВЛЕНО: Добавлены недостающие административные роуты
    Route::post('/cache/clear', [AdminController::class, 'clearCache'])->name('cache.clear');
    Route::post('/backup/create', [AdminController::class, 'createBackup'])->name('backup.create');
    Route::get('/settings/export', [AdminSettingController::class, 'export'])->name('settings.export');
    Route::post('/settings/import', [AdminSettingController::class, 'import'])->name('settings.import');
    Route::get('/logs/view', [AdminController::class, 'logs'])->name('logs.view');
    Route::post('/logs/clear', [AdminController::class, 'clearLogs'])->name('logs.clear');
    Route::get('/logs/download', [AdminController::class, 'downloadLogs'])->name('logs.download');

    // Reports management
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
    Route::post('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/{report}/reject', [AdminReportController::class, 'reject'])->name('reports.reject');
    Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');
    Route::post('/reports/bulk-action', [AdminReportController::class, 'bulkAction'])->name('reports.bulk-action');
    Route::post('/reports/resolve-all', [AdminReportController::class, 'resolveAll'])->name('reports.resolve-all');
});

// Authentication routes
require __DIR__.'/auth.php';

// Add this with your other routes
Route::get('storage/{path}', [StorageController::class, 'serve'])
    ->where('path', '.*')
    ->name('storage.serve');