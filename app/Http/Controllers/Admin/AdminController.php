<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Topic;
use App\Models\Post;
use App\Models\Category;
use App\Models\PrivateMessage;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Статистика пользователей
        $usersStats = [
            'total' => User::count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'online' => User::online()->count(),
            'banned' => User::banned()->count(),
            'admins' => User::admins()->count(),
            'moderators' => User::moderators()->count(),
        ];

        // Статистика контента
        $contentStats = [
            'topics' => Topic::count(),
            'posts' => Post::count(),
            'categories' => Category::count(),
            'topics_today' => Topic::whereDate('created_at', today())->count(),
            'posts_today' => Post::whereDate('created_at', today())->count(),
        ];

        // Статистика сообщений
        $messagesStats = [
            'total' => PrivateMessage::count(),
            'today' => PrivateMessage::whereDate('created_at', today())->count(),
            'unread' => PrivateMessage::unread()->count(),
        ];

        // Статистика жалоб
        $reportsStats = [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'today' => Report::whereDate('created_at', today())->count(),
        ];

        // Последние пользователи
        $recentUsers = User::latest()->limit(5)->get();

        // Последние темы
        $recentTopics = Topic::with('user', 'category')->latest()->limit(5)->get();

        // Последние жалобы
        $recentReports = Report::with('reporter')->where('status', 'pending')->latest()->limit(5)->get();

        // Системная информация
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_size' => $this->getDatabaseSize(),
            'storage_used' => $this->getStorageUsed(),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
        ];

        return view('admin.dashboard', compact(
            'usersStats',
            'contentStats', 
            'messagesStats',
            'reportsStats',
            'recentUsers',
            'recentTopics',
            'recentReports',
            'systemInfo'
        ));
    }

    /**
     * ИСПРАВЛЕНО: Добавлен метод очистки кеша
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');
        
        try {
            switch ($type) {
                case 'config':
                    Artisan::call('config:clear');
                    $message = 'Кеш конфигурации очищен';
                    break;
                    
                case 'route':
                    Artisan::call('route:clear');
                    $message = 'Кеш роутов очищен';
                    break;
                    
                case 'view':
                    Artisan::call('view:clear');
                    $message = 'Кеш представлений очищен';
                    break;
                    
                case 'cache':
                    Artisan::call('cache:clear');
                    $message = 'Кеш приложения очищен';
                    break;
                    
                case 'all':
                default:
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    Artisan::call('cache:clear');
                    $message = 'Весь кеш очищен';
                    break;
            }
            
            Log::info("Admin cache cleared", [
                'type' => $type,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error("Cache clear failed", [
                'type' => $type,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка очистки кеша: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Ошибка очистки кеша: ' . $e->getMessage());
        }
    }

    /**
     * ИСПРАВЛЕНО: Добавлен метод создания резервной копии
     */
    public function createBackup(Request $request)
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "forum_backup_{$timestamp}.sql";
            
            // Создаем директорию для бэкапов, если её нет
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }
            
            $backupPath = $backupDir . '/' . $filename;
            
            // Получаем настройки базы данных
            $host = config('database.connections.pgsql.host');
            $port = config('database.connections.pgsql.port', 5432);
            $database = config('database.connections.pgsql.database');
            $username = config('database.connections.pgsql.username');
            $password = config('database.connections.pgsql.password');
            
            // Команда для создания дампа PostgreSQL
            $command = sprintf(
                'PGPASSWORD="%s" pg_dump -h %s -p %s -U %s -d %s > %s',
                $password,
                $host,
                $port,
                $username,
                $database,
                $backupPath
            );
            
            // Выполняем команду
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                throw new \Exception('Failed to create backup');
            }
            
            // Очищаем старые бэкапы
            $this->cleanupOldBackups();
            
            Log::info("Database backup created", [
                'filename' => $filename,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Резервная копия создана успешно'
                ]);
            }
            
            return back()->with('success', 'Резервная копия создана успешно');
            
        } catch (\Exception $e) {
            Log::error("Backup creation failed", [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка создания резервной копии: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Ошибка создания резервной копии: ' . $e->getMessage());
        }
    }

    /**
     * ИСПРАВЛЕНО: Добавлен метод просмотра логов
     */
    public function logs(Request $request)
    {
        $logFiles = File::files(storage_path('logs'));
        $logs = [];
        
        foreach ($logFiles as $file) {
            $logs[] = [
                'name' => $file->getFilename(),
                'size' => $this->formatFileSize($file->getSize()),
                'modified' => date('Y-m-d H:i:s', $file->getMTime())
            ];
        }
        
        return view('admin.logs', compact('logs'));
    }

    /**
     * ИСПРАВЛЕНО: Добавлен метод очистки логов
     */
    public function clearLogs(Request $request)
    {
        try {
            $logFiles = File::files(storage_path('logs'));
            
            foreach ($logFiles as $file) {
                if ($file->getExtension() === 'log') {
                    File::delete($file->getPathname());
                }
            }
            
            Log::info("Logs cleared", [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Логи успешно очищены'
                ]);
            }
            
            return back()->with('success', 'Логи успешно очищены');
            
        } catch (\Exception $e) {
            Log::error("Log clearing failed", [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка очистки логов: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Ошибка очистки логов: ' . $e->getMessage());
        }
    }

    /**
     * ИСПРАВЛЕНО: Добавлен метод скачивания логов
     */
    public function downloadLogs(Request $request)
    {
        try {
            $filename = $request->query('file');
            $path = storage_path('logs/' . $filename);
            
            if (!File::exists($path)) {
                throw new \Exception('Log file not found');
            }
            
            return response()->download($path);
            
        } catch (\Exception $e) {
            Log::error("Log download failed", [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);
            
            return back()->with('error', 'Ошибка скачивания лога: ' . $e->getMessage());
        }
    }

    /**
     * Получить размер базы данных
     */
    private function getDatabaseSize()
    {
        try {
            $result = \DB::select("SELECT pg_size_pretty(pg_database_size(current_database())) as size");
            return $result[0]->size;
        } catch (\Exception $e) {
            Log::error("Failed to get database size", ['error' => $e->getMessage()]);
            return 'N/A';
        }
    }

    /**
     * Получить объем использованного хранилища
     */
    private function getStorageUsed()
    {
        try {
            $size = $this->getDirectorySize(storage_path());
            return $this->formatFileSize($size);
        } catch (\Exception $e) {
            Log::error("Failed to get storage size", ['error' => $e->getMessage()]);
            return 'N/A';
        }
    }

    /**
     * Получить размер директории
     */
    private function getDirectorySize($path)
    {
        $size = 0;
        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    /**
     * Форматировать размер файла
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Очистить старые бэкапы
     */
    private function cleanupOldBackups()
    {
        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);
        
        // Сортируем файлы по времени изменения
        usort($files, function ($a, $b) {
            return $b->getMTime() - $a->getMTime();
        });
        
        // Оставляем только 5 последних бэкапов
        $maxBackups = 5;
        if (count($files) > $maxBackups) {
            foreach (array_slice($files, $maxBackups) as $file) {
                File::delete($file->getPathname());
            }
        }
    }
}