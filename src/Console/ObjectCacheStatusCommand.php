<?php

namespace LeoColomb\WPAcornCache\Console;

use LeoColomb\WPAcornCache\Caches\ObjectCache;
use Roots\Acorn\Console\Commands\Command;

class ObjectCacheStatusCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'object-cache:status';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get object cache status';

    /**
     * Execute the console command.
     *
     * @param ObjectCache $cache The cache interface
     * @return void
     */
    public function handle(ObjectCache $cache)
    {
        try {
            $cache->add('status', 'ok');
            $valid = $cache->get('status') === 'ok';
        } catch (\Exception $exception) {
            $this->error('Status: Error');
            $this->alert($exception);

            return;
        }

        if (!$valid) {
            $this->warn('Status: Unexpected behavior');

            return;
        }

        $this->info('Status: Connected');
    }
}
