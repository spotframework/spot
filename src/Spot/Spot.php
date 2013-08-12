<?php
namespace Spot;

use Spot\Reflect\Reflection;
use Spot\Code\CodeStorage;
use Doctrine\Common\Cache\Cache;
use Spot\Inject\Impl\InjectorImpl;
use Spot\Inject\Impl\BuiltInModule;
use Spot\App\Web\Impl\WebAppImpl;

class Spot {
    const DEV_MODE = 'dev';
    const PROD_MODE = 'prod';
    
    private $cache,
            $constants,
            $reflection,
            $codeStorage;
    
    public function __construct(
            Cache $cache,
            array $constants,
            Reflection $reflection, 
            CodeStorage $codeStorage) {
        $this->cache = $cache;
        $this->constants = $constants;
        $this->reflection = $reflection;
        $this->codeStorage = $codeStorage;
    }
    
    /**
     * @return \Spot\Inject\Injector
     */
    public function createInjector() {        
        $modules = array_merge(
            [new BuiltInModule($this->constants, $this->cache, $this->reflection, $this->codeStorage), ],
            func_get_args()
        );
        return InjectorImpl::create(
            $modules,
            $this->reflection,
            $this->codeStorage
        );
    }
    
    public function createWebApp() {
        return new WebAppImpl([$this, "createInjector"], array_merge(
            func_get_args(), 
            [
                "Spot\Module\Spot\DomainModule",
                "Spot\Module\Symfony\ValidatorModule",
                "Spot\App\Web\Impl\WebAppModule",
            ]
        ));
    }
    
    public function createCliApp() {
        $injector = call_user_func_array(
            [$this, "createInjector"],
            array_merge(func_get_args(), ["Spot\App\Cli\Impl\CliModule"])
        );

        return $injector->getInstance("Spot\App\Cli\CliApp");
    }
    
    public function createRestApp() {
        return call_user_func_array(
            [$this, "createWebApp"],
            array_merge(func_get_args(), ["Spot\App\Rest\Impl\RestAppModule"])
        );
    }
    
    public function createZeroMQApp() {
        
    }
}