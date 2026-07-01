<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\StringHelper;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('escola', function () {
            return new \App\Services\Escola();
        });
        $this->app->singleton('qlib', function () {
            return new \App\Services\Qlib();
        });
        // $this->app->singleton(StringHelper::class, function () {
        //     return new StringHelper();
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usa o modelo customizado que aponta para o banco 'tenant'
        // para que o Sanctum consiga resolver tokens criados no contexto multi-tenant
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Ensina o Sanctum a buscar o token tanto no Header quanto no parâmetro ?token= da URL
        Sanctum::getAccessTokenFromRequestUsing(function ($request) {
            return $request->bearerToken() ?? $request->query('token');
        });
    }
}
