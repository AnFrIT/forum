<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Topic;
use App\Models\Post;
use App\Models\Report;
use App\Policies\TopicPolicy;
use App\Policies\PostPolicy;
use App\Policies\ReportPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Topic::class => TopicPolicy::class,
        Report::class => ReportPolicy::class,
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for admin and moderator abilities
        Gate::define('lock topics', function ($user) {
            return $user->isModerator() && !$user->isBanned();
        });

        Gate::define('pin topics', function ($user) {
            return $user->isModerator() && !$user->isBanned();
        });

        Gate::define('edit any topics', function ($user) {
            return $user->isModerator() && !$user->isBanned();
        });

        Gate::define('delete any topics', function ($user) {
            return $user->isModerator() && !$user->isBanned();
        });

        Gate::define('moderate topics', function ($user) {
            return $user->isModerator() && !$user->isBanned();
        });
        
        // Add 'manage reports' gate
        Gate::define('manage reports', function ($user) {
            return $user->isModerator() && !$user->isBanned();
        });
    }
}
