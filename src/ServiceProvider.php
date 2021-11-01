<?php

namespace LeoColomb\WPAcornCache;

use Roots\Acorn\Application;
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
        $this->app->singleton(ObjectCache::class, function () {
            return new ObjectCache($this->app->config->get('object-cache'));
        });
        // TODO: Lazy load
        $this->app->singleton(PageCache::class, function () {
            return new PageCache(app('http'), $this->app->config->get('page-cache'));
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
            dirname(__DIR__) . '/config/object-cache.php' => $this->app->configPath('object-cache.php'),
            dirname(__DIR__) . '/config/page-cache.php' => $this->app->configPath('page-cache.php'),
        ]);
    }
}
