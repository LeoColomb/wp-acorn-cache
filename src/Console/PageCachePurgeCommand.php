<?php

namespace LeoColomb\WPAcornCache\Console;

use LeoColomb\WPAcornCache\Caches\PageCache;
use Roots\Acorn\Console\Commands\Command;

class PageCachePurgeCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'page-cache:purge
                            {url? : The URL to purge}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Purge page cache';

    /**
     * Execute the console command.
     *
     * @param PageCache $cache The cache interface
     * @return void
     */
    public function handle(PageCache $cache): void
    {
        if ($this->hasArgument('url')) {
            $cache->getStore()->purge($this->argument('url'));
        } else {
            $cache->getStore()->cleanup();
        }

        $this->info('Purged');
    }
}
