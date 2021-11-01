<?php

namespace LeoColomb\WPAcornCache\Providers;

use LeoColomb\WPAcornCache\Console\ObjectCacheCommand;
use LeoColomb\WPAcornCache\Console\PageCacheCommand;
use LeoColomb\WPAcornCache\ObjectCache;
use LeoColomb\WPAcornCache\PageCache;
use Roots\Acorn\ServiceProvider;

class AcornCacheServiceProvider extends ServiceProvider
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
            dirname(__DIR__, 2) . '/config/object-cache.php' => $this->app->configPath('object-cache.php'),
            dirname(__DIR__, 2) . '/config/page-cache.php' => $this->app->configPath('page-cache.php'),
        ]);

        $this->commands([
            ObjectCacheCommand::class,
            PageCacheCommand::class,
        ]);
    }
}
