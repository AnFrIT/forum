<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class MaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Получаем статус сайта из настроек
        $siteStatus = Setting::where('key', 'site_status')->value('value') ?? 'online';
        
        // Если сайт не в режиме обслуживания - пропускаем
        if ($siteStatus !== 'maintenance') {
            return $next($request);
        }

        // ИСКЛЮЧАЕМ АДМИНСКИЕ РОУТЫ - админы всегда проходят
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        // ИСКЛЮЧАЕМ API РОУТЫ - нужны для работы админки
        if ($request->is('api/*')) {
            return $next($request);
        }
        
        // Проверяем, авторизован ли пользователь и является ли он админом/модератором
        if (auth()->check()) {
            $user = auth()->user();
            // АДМИНЫ И МОДЕРАТОРЫ ВСЕГДА ПРОХОДЯТ
            if ($user->is_admin || $user->is_moderator) {
                return $next($request);
            }
        }
        
        // Исключаем служебные роуты (авторизация)
        $excludedRoutes = [
            'login',
            'logout', 
            'password.request',
            'password.email',
            'password.reset',
            'password.store',
            'language.switch',
            'api.maintenance-status'
        ];
        
        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, $excludedRoutes)) {
            return $next($request);
        }

        // Исключаем путь к логину
        if ($request->is('login') || $request->is('register') || $request->is('password/*') || $request->is('language/*')) {
            return $next($request);
        }
        
        // Если это API запрос
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Сайт находится на техническом обслуживании',
                'status' => 'maintenance'
            ], 503);
        }
        
        // Получаем сообщение об обслуживании
        $maintenanceMessage = Setting::where('key', 'maintenance_message')->value('value') 
            ?? 'Сайт находится на техническом обслуживании. Приносим извинения за неудобства.';
        
        // Возвращаем страницу обслуживания
        return response()->view('maintenance', [
            'message' => $maintenanceMessage
        ], 503);
    }
}