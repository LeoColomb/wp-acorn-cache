<?php

namespace LeoColomb\WPAcornCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Manages HTTP cache objects in a Container.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class PageCache extends HttpCache
{
    protected $cacheDir;
    protected $kernel;

    private $store;
    private $surrogate;
    private $options;

    /**
     * @param array|string|StoreInterface $cache The cache directory (default used if null) or the storage instance
     */
    public function __construct(KernelInterface $kernel, $cache = null, array $options = null, SurrogateInterface $surrogate = null)
    {
        $this->kernel = $kernel;
        $this->surrogate = $surrogate;
        $this->options = $options ?? [];

        if ($cache instanceof StoreInterface) {
            $this->store = $cache;
        } elseif (is_array($cache)) {
            $this->options = $cache;
        } elseif (null !== $cache && !\is_string($cache)) {
            throw new \TypeError(sprintf('Argument 2 passed to "%s()" must be a string or a SurrogateInterface, "%s" given.', __METHOD__, get_debug_type($cache)));
        } else {
            $this->cacheDir = $cache;
        }

        if (null === $options && $kernel->isDebug()) {
            $this->options = ['debug' => true];
        }

        if ($this->options['debug'] ?? false) {
            $this->options += ['stale_if_error' => 0];
        }

        parent::__construct($kernel, $this->createStore(), $this->surrogate, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    protected function forward(Request $request, bool $catch = false, Response $entry = null)
    {
        $this->getKernel()->boot();
        $this->getKernel()->getContainer()->set('cache', $this);

        return parent::forward($request, $catch, $entry);
    }

    /**
     * @return Store|StoreInterface
     */
    protected function createStore()
    {
        return $this->store ?? new Store($this->cacheDir ?: $this->kernel->getCacheDir() . '/http_cache');
    }
}
