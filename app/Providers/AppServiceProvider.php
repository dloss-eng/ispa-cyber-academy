<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{Schema, Gate, URL};
use Illuminate\Pagination\Paginator;
use App\Models\{User, Module, Badge, Certificate, Etablissement, Signalement, Subscription, UserNotification, ForumTopic, Quiz};
use App\Models\Classe;
use App\Policies\{UserPolicy, ModulePolicy, BadgePolicy, CertificatePolicy, EtablissementPolicy, SignalementPolicy, SubscriptionPolicy, UserNotificationPolicy, ForumTopicPolicy, QuizPolicy, ClassePolicy};

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\GamificationService::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useTailwind();

        // ✅ Forcer HTTPS en production (nécessaire pour Render)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }

        // ✅ Enregistrement de toutes les Policies
        Gate::policy(User::class,             UserPolicy::class);
        Gate::policy(Module::class,           ModulePolicy::class);
        Gate::policy(Badge::class,            BadgePolicy::class);
        Gate::policy(Certificate::class,      CertificatePolicy::class);
        Gate::policy(Etablissement::class,    EtablissementPolicy::class);
        Gate::policy(Signalement::class,      SignalementPolicy::class);
        Gate::policy(Subscription::class,     SubscriptionPolicy::class);
        Gate::policy(UserNotification::class, UserNotificationPolicy::class);
        Gate::policy(ForumTopic::class,       ForumTopicPolicy::class);
        Gate::policy(Quiz::class,             QuizPolicy::class);
        Gate::policy(Classe::class,           ClassePolicy::class);

        // ✅ Super-admin bypass — admin passe toutes les policies
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdmin()) return true;
        });
    }
}
