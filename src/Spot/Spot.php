<?php
namespace Spot;

use Spot\App\Web\WebApp;
use Spot\Module\Spot\SpotModule;
use Spot\Gen\CodeStorage;
use Spot\Inject\Impl\InjectorImpl;
use Spot\Inject\Injector;
use Spot\Reflect\Impl\ReflectionImpl;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\Common\Cache\XcacheCache;
use Doctrine\Common\Cache\ApcCache;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Spot {
    const DEV_MODE = "dev";
    const PROD_MODE = "prod";

    private $mode,
            $dump,
            $cache,
            $storage,
            $logger;

    private function __construct(
            $mode,
            $dump,
            Cache $cache,
            CodeStorage $storage,
            LoggerInterface $logger = null) {
        $this->mode = $mode;
        $this->dump = $dump;
        $this->cache = $cache;
        $this->storage = $storage;
        $this->logger = $logger ?: new NullLogger();
        $this->dump = $dump;
    }

    public function isDebug() {
        return $this->mode === self::DEV_MODE;
    }

    public function getDumpDir() {
        return $this->dump;
    }

    /**
     * Create an injector
     *
     * @param array $modules
     * @return Injector
     */
    public function createInjector(array $modules = []) {
        $reflection = ReflectionImpl::create($this->cache);

        return InjectorImpl::create(array_merge(
                [
                    new SpotModule(
                        $reflection,
                        $this->storage,
                        $this->cache,
                        $this->isDebug(),
                        $this->dump
                    ),
                    "Spot\\Domain\\DomainModule"
                ],
                $modules),
            $reflection,
            $this->storage,
            $this->logger
        );
    }

    /**
     * Create a spot web app instance
     *
     * @return WebApp
     */
    public function createWebApp() {
        $modules = array_merge(array_merge(
            ["Spot\\App\\Web\\WebAppModule"],
            func_get_args()
        ));

        return $this->createInjector($modules)
                    ->getInstance("Spot\\App\\Web\\WebApp");
    }

    public function createRESTApp() {
        $modules = array_merge(array_merge(
            [
                "Spot\\App\\Web\\WebAppModule",
                "Spot\\App\\REST\\RESTAppModule"
            ],
            func_get_args()
        ));

        return $this->createInjector($modules)
                    ->getInstance("Spot\\App\\REST\\RESTApp");
    }

    static public function buildDev() {
        $dump = sys_get_temp_dir() . time();
        $cache = new ArrayCache();
        $storage = CodeStorage::create($dump);

        return new Spot(Spot::DEV_MODE, $dump, $cache, $storage);
    }

    static public function buildProd($dump = null) {
        $dump = $dump ?: (sys_get_temp_dir() . posix_getppid());
        $storage = CodeStorage::create($dump);

        if(extension_loaded("apc")) {
            $cache = new ApcCache("Spot");
        } else if(extension_loaded("xcache")) {
            $cache = new XcacheCache();
        } else {
            $cache = new PhpFileCache($dump);
        }

        return new Spot(Spot::PROD_MODE, $dump, $cache, $storage);
    }
}
