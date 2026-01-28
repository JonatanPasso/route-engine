<?php

declare(strict_types=1);

namespace JonatanPasso\RouteEngine\Providers;

use Illuminate\Support\ServiceProvider;
use JonatanPasso\RouteEngine\Services\RouteService;

class RouteEngineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Publica as configurações: php artisan vendor:publish --tag=route-config
        $this->publishes([
            __DIR__ . '/../../config/route-engine.php' => config_path('route-engine.php'),
        ], 'route-config');

        // Carrega as migrations do pacote automaticamente
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/route-engine.php', 'route-engine');

        // Registro do serviço para Injeção de Dependência
        $this->app->singleton(RouteService::class, fn ($app) => new RouteService());
    }
}
