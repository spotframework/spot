<?php
namespace Spot;

use Spot\Reflect\Reflection;
use Spot\Code\CodeStorage;
use Spot\Inject\Impl\InjectorImpl;

class Spot {
    const DEV_MODE = 'dev';
    const PROD_MODE = 'prod';
    
    private $constants,
            $reflection,
            $codeStorage;
    
    public function __construct(
            array $constants,
            Reflection $reflection, 
            CodeStorage $codeStorage) {
        $this->constants = $constants;
        $this->reflection = $reflection;
        $this->codeStorage = $codeStorage;
    }
    
    /**
     * @return \Spot\Inject\Injector
     */
    public function createInjector() {        
        return InjectorImpl::create(
            func_get_args(),
            $this->constants,
            $this->reflection,
            $this->codeStorage
        );
    }
    
    public function createWebApp() {
        $injector = call_user_func_array(
            [$this, "createInjector"],
            array_merge(func_get_args(), ["Spot\App\Web\Impl\WebAppModule"])
        );
        
        return $injector->getInstance("Spot\App\Web\WebApp");
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