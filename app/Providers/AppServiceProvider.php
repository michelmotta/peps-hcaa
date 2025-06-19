<?php

namespace App\Providers;

use App\Enums\LessonStatusEnum;
use App\Models\Lesson;
use App\Models\User;
use App\Policies\LessonPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Lesson::class => LessonPolicy::class,
        User::class => UserPolicy::class,
    ];

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
        Paginator::useBootstrapFive();

        require base_path('routes/breadcrumbs.php');

        View::composer('*', function ($view) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if ($user) {
                $user->load('subscribedLessons');
            }
            $view->with('user', $user);
        });

        View::composer(['web.class'], function ($view) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            $watchedTopicIds = $user ? $user->histories()->pluck('topic_id')->toArray() : [];
            $view->with('watchedTopicIds', $watchedTopicIds);
        });
    }
}
