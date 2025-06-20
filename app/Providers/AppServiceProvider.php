<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap for pagination
        Paginator::useBootstrapFive();

        // Share settings with all views
        View::composer('*', function ($view) {
            try {
                $view->with('globalSettings', Setting::all()->pluck('value', 'key'));
            } catch (\Exception $e) {
                // Database might not be migrated yet
                $view->with('globalSettings', collect());
            }
        });

        // Custom Blade directives
        Blade::directive('setting', function ($expression) {
            return "<?php echo setting($expression); ?>";
        });

        // Force HTTPS in production
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        // Глобальная переменная для всех шаблонов
        View::composer('*', function ($view) {
            $view->with('currentUser', auth()->user());
            
            // Добавляем популярные категории для использования в шаблонах ошибок
            try {
                $popularCategories = \App\Models\Category::getPopular(6);
                $view->with('popularCategories', $popularCategories);
            } catch (\Exception $e) {
                // Если произошла ошибка, передаем пустую коллекцию
                $view->with('popularCategories', collect());
            }
        });
    }
}
