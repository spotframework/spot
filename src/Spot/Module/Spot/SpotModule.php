<?php
namespace Spot\Module\Spot;

use Spot\Inject\Named;
use Spot\Gen\CodeStorage;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Reflect\Reflection;
use Doctrine\Common\Cache\Cache;

class SpotModule {
    private $reflection,
        $storage,
        $cache,
        $debug,
        $dump;

    public function __construct(
        Reflection $reflection,
        CodeStorage $storage,
        Cache $cache,
        $debug,
        $dump) {
        $this->reflection = $reflection;
        $this->storage = $storage;
        $this->cache = $cache;
        $this->debug = $debug;
        $this->dump = $dump;
    }

    /** @Provides("Spot\Reflect\Reflection") @Singleton */
    public function provideReflection() {
        return $this->reflection;
    }

    /** @Provides("Spot\Gen\CodeStorage") @Singleton */
    public function provideCodeStorage() {
        return $this->storage;
    }

    /** @Provides("Doctrine\Common\Cache\Cache") @Singleton */
    public function provideCache() {
        return $this->cache;
    }

    /** @Provides @Named("app.debug") @Singleton */
    public function provideDebug() {
        return $this->debug;
    }

    /** @Provides @Named("app.dump-dir") @Singleton */
    public function provideDumpDir() {
        return $this->dump;
    }
}
