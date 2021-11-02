<?php

namespace LeoColomb\WPAcornCache\Providers;

use LeoColomb\WPAcornCache\Console\ObjectCachePurgeCommand;
use LeoColomb\WPAcornCache\Console\ObjectCacheStatusCommand;
use LeoColomb\WPAcornCache\Console\PageCachePurgeCommand;
use LeoColomb\WPAcornCache\Caches\ObjectCache;
use LeoColomb\WPAcornCache\Caches\PageCache;
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

        if ($this->app->runningInConsole()) {
            $this->commands([
                ObjectCacheStatusCommand::class,
                ObjectCachePurgeCommand::class,
                PageCachePurgeCommand::class,
            ]);
        }
    }
}
