<?php

namespace LeoColomb\WPAcornCache\Console;

use LeoColomb\WPAcornCache\Caches\ObjectCache;
use Roots\Acorn\Console\Commands\Command;

class ObjectCachePurgeCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'object-cache:purge
                            {key? : The key to purge}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Purge object cache';

    /**
     * Execute the console command.
     *
     * @param ObjectCache $cache The cache interface
     * @return void
     */
    public function handle(ObjectCache $cache): void
    {
        if ($this->hasArgument('key')) {
            $cache->delete($this->argument('key'));
        } else {
            $cache->flush();
        }

        $this->info('Purged');
    }
}
