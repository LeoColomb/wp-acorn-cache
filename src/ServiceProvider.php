<?php

namespace LeoColomb\WPAcornCache;

use Roots\Acorn\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(Container::class, function () {
            return new Container($this->config());
        });
        $this->app->singleton(PageCache::class, function () {
            return new PageCache($this->config());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/wp-cache.php' => $this->app->configPath('wp-cache.php')
        ]);
    }

    /**
     * Return the services config.
     *
     * @return array
     */
    protected function config(): array
    {
        return collect([
            'path' => $this->app->basePath('dist')
        ])
            ->merge($this->app->config->get('wp-cache', []))
            ->all();
    }
}
