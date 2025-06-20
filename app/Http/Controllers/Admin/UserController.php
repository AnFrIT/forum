<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'moderator') {
                $query->where('is_moderator', true);
            }
        }

        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->where('is_banned', true);
            } elseif ($request->status === 'active') {
                $query->where('is_banned', false);
            }
        }

        // Сортировка
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        switch ($sortField) {
            case 'name':
                $query->orderBy('name', $sortDirection);
                break;
            case 'posts_count':
                $query->orderBy('posts_count', $sortDirection);
                break;
            case 'last_activity':
                $query->orderBy('last_activity_at', $sortDirection);
                break;
            default:
                $query->latest();
        }

        $users = $query->paginate(20);

        // ИСПРАВЛЕНО: Простая коллекция ролей
        $roles = collect([
            (object) ['name' => 'admin', 'display_name' => 'Администратор'],
            (object) ['name' => 'moderator', 'display_name' => 'Модератор'],
            (object) ['name' => 'user', 'display_name' => 'Пользователь'],
        ]);

        // Если запрос на экспорт
        if ($request->get('export') === 'csv') {
            return $this->exportUsers($users->items());
        }

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        // ИСПРАВЛЕНО: Получаем статистику через связи
        $stats = [
            'topics_count' => $user->topics()->count(),
            'posts_count' => $user->posts()->count(),
            'last_activity' => $user->last_activity_at,
            'registered_at' => $user->created_at,
        ];

        $recentTopics = $user->topics()
            ->with('category')
            ->latest()
            ->limit(5)
            ->get();

        $recentPosts = $user->posts()
            ->with('topic')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.users.show', compact('user', 'stats', 'recentTopics', 'recentPosts'));
    }

    public function edit(User $user)
    {
        $roles = collect([
            (object) ['name' => 'admin', 'display_name' => 'Администратор'],
            (object) ['name' => 'moderator', 'display_name' => 'Модератор'],
            (object) ['name' => 'user', 'display_name' => 'Пользователь'],
        ]);

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'preferred_language' => 'required|in:ru,en,ar',
            'is_admin' => 'boolean',
            'is_moderator' => 'boolean',
            'is_banned' => 'boolean',
            'ban_reason' => 'required_if:is_banned,1|string|max:255',
            'ban_duration' => 'required_if:is_banned,1'
        ]);

        // Prevent self-demotion from admin
        if ($user->id === auth()->id() && $user->is_admin && !($request->has('is_admin') && $request->is_admin)) {
            return back()->with('error', 'Вы не можете снять права администратора с самого себя!');
        }

        // Update basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        $user->bio = $validated['bio'];
        $user->website = $validated['website'];
        $user->preferred_language = $validated['preferred_language'];

        // Update roles if user is admin
        if (auth()->user()->is_admin) {
            $user->is_admin = $request->has('is_admin');
            $user->is_moderator = $request->has('is_moderator');
        }

        // Handle ban status
        if ($request->has('is_banned')) {
            if ($request->is_banned) {
                // Calculate ban expiration
                $expiresAt = null;
                if ($validated['ban_duration'] !== 'permanent') {
                    $expiresAt = now()->addDays((int)$validated['ban_duration']);
                }
                
                $user->ban($validated['ban_reason'], $expiresAt);
            } else {
                $user->unban();
            }
        }

        $user->save();

        Log::info('User updated by admin', [
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'changes' => $validated
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно обновлен!');
    }

    public function ban(Request $request, User $user)
    {
        // Предотвращаем блокировку самого себя
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Вы не можете заблокировать себя!');
        }

        // Предотвращаем блокировку других админов
        if ($user->is_admin && !auth()->user()->is_admin) {
            return back()->with('error', 'Вы не можете заблокировать администратора!');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:1',
        ]);

        $expiresAt = null;
        if ($request->filled('duration') && $request->duration !== 'permanent') {
            $expiresAt = now()->addDays($validated['duration']);
        }

        $user->ban($validated['reason'], $expiresAt);

        Log::info('User banned by admin', [
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'reason' => $validated['reason'],
            'expires_at' => $expiresAt
        ]);

        return back()->with('success', 'Пользователь заблокирован!');
    }

    public function unban(User $user)
    {
        $user->unban();

        Log::info('User unbanned by admin', [
            'user_id' => $user->id,
            'admin_id' => auth()->id()
        ]);

        return back()->with('success', 'Пользователь разблокирован!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Вы не можете удалить свой аккаунт!');
        }

        if ($user->is_admin) {
            return back()->with('error', 'Нельзя удалить администратора!');
        }

        $userName = $user->name;
        $user->delete();

        Log::info('User deleted by admin', [
            'user_id' => $user->id,
            'user_name' => $userName,
            'admin_id' => auth()->id()
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно удален!');
    }

    public function addModerator(User $user)
    {
        if ($user->is_admin) {
            return back()->with('error', 'Пользователь уже является администратором!');
        }

        if ($user->is_moderator) {
            return back()->with('error', 'Пользователь уже является модератором!');
        }

        $user->update(['is_moderator' => true]);

        Log::info('User promoted to moderator', [
            'user_id' => $user->id,
            'admin_id' => auth()->id()
        ]);

        return back()->with('success', 'Пользователь назначен модератором!');
    }

    public function removeModerator(User $user)
    {
        if ($user->is_admin) {
            return back()->with('error', 'Нельзя снять права с администратора!');
        }

        if (!$user->is_moderator) {
            return back()->with('error', 'Пользователь не является модератором!');
        }

        $user->update(['is_moderator' => false]);

        Log::info('User demoted from moderator', [
            'user_id' => $user->id,
            'admin_id' => auth()->id()
        ]);

        return back()->with('success', 'Права модератора сняты!');
    }

    /**
     * ИСПРАВЛЕНО: Добавлен метод массовых действий
     */
    public function bulkAction(Request $request)
    {
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
        $errors = [];

        foreach ($users as $user) {
            try {
                switch ($request->action) {
                    case 'ban':
                        if ($user->is_admin) {
                            $errors[] = "Нельзя заблокировать администратора {$user->name}";
                            continue 2;
                        }
                        
                        $expiresAt = $request->duration ? now()->addDays($request->duration) : null;
                        $user->ban($request->reason ?: 'Массовая блокировка', $expiresAt);
                        $successCount++;
                        break;
                        
                    case 'unban':
                        $user->unban();
                        $successCount++;
                        break;
                        
                    case 'make-moderator':
                        if ($user->is_admin) {
                            $errors[] = "{$user->name} уже администратор";
                            continue 2;
                        }
                        if ($user->is_moderator) {
                            $errors[] = "{$user->name} уже модератор";
                            continue 2;
                        }
                        
                        $user->update(['is_moderator' => true]);
                        $successCount++;
                        break;
                        
                    case 'remove-moderator':
                        if ($user->is_admin) {
                            $errors[] = "Нельзя снять права с администратора {$user->name}";
                            continue 2;
                        }
                        if (!$user->is_moderator) {
                            $errors[] = "{$user->name} не является модератором";
                            continue 2;
                        }
                        
                        $user->update(['is_moderator' => false]);
                        $successCount++;
                        break;
                        
                    case 'delete':
                        if ($user->is_admin) {
                            $errors[] = "Нельзя удалить администратора {$user->name}";
                            continue 2;
                        }
                        
                        $user->delete();
                        $successCount++;
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = "Ошибка с пользователем {$user->name}: " . $e->getMessage();
                Log::error('Bulk action error', [
                    'user_id' => $user->id,
                    'action' => $request->action,
                    'error' => $e->getMessage(),
                    'admin_id' => auth()->id()
                ]);
            }
        }

        Log::info('Bulk action performed', [
            'action' => $request->action,
            'total_users' => count($users),
            'success_count' => $successCount,
            'admin_id' => auth()->id()
        ]);

        $message = "Обработано {$successCount} из " . count($users) . " пользователей";
        
        if (!empty($errors)) {
            $message .= '. Ошибки: ' . implode('; ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= ' и еще ' . (count($errors) - 3) . ' ошибок...';
            }
        }

        return response()->json([
            'success' => true,
            'processed' => $successCount,
            'total' => count($users),
            'errors' => $errors,
            'message' => $message
        ]);
    }

    /**
     * ИСПРАВЛЕНО: Добавлен метод экспорта пользователей
     */
    private function exportUsers($users)
    {
        $filename = 'forum_users_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            
            // Заголовки CSV
            fputcsv($file, [
                'ID',
                'Имя',
                'Email',
                'Роль',
                'Статус',
                'Дата регистрации',
                'Последняя активность',
                'Количество постов',
                'Количество тем',
                'Репутация'
            ]);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role_name,
                    $user->is_banned ? 'Заблокирован' : 'Активен',
                    $user->created_at->format('d.m.Y H:i'),
                    $user->last_activity_at ? $user->last_activity_at->format('d.m.Y H:i') : 'Никогда',
                    $user->posts_count ?? 0,
                    $user->topics_count ?? 0,
                    $user->reputation ?? 0
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}