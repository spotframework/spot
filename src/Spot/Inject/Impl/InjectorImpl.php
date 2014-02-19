<?php
namespace Spot\Inject\Impl;

use Spot\Gen\CodeStorage;
use Spot\Inject\Impl\Aspect\AspectWeaver;
use Spot\Inject\Impl\Binders\ConfigBinder;
use Spot\Inject\Impl\Binders\JustInTimeBinder;
use Spot\Inject\Impl\Binders\ModuleBinder;
use Spot\Inject\Impl\Visitors\CircularProviderVisitor;
use Spot\Inject\Impl\Visitors\LateBindingVisitor;
use Spot\Inject\Impl\Visitors\ConstantFoldingVisitor;
use Spot\Inject\Impl\Visitors\SingletonMarkerVisitor;
use Spot\Inject\Injector;
use Spot\Inject\Key;
use Spot\Reflect\Reflection;
use Psr\Log\LoggerInterface;

class InjectorImpl implements Injector {
    private $modules,
            $singletons,
            $factory,
            $lazy;

    protected function __construct(
            Modules $modules,
            Singletons $singletons,
            FactoryFactory $factory,
            LazyFactory $lazy) {
        $this->modules = $modules;
        $this->singletons = $singletons;
        $this->factory = $factory;
        $this->lazy = $lazy;
    }

    function get(Key $key) {
        $factory = $this->factory->getFactory($key);

        return $factory::get($this->singletons, $this->modules, $this, $this->factory->getAspect());
    }

    function getInstance($type) {
        return $this->get(Key::ofType($type));
    }

    function getLazy(Key $key) {
        $lazyClass = $this->lazy->get($key);

        return new $lazyClass($this, $key);
    }

    function fork(array $modules) {
        if(empty($modules)) {
            return $this;
        }

        $modules = new Modules(array_merge(
            iterator_to_array($this->modules),
            $modules
        ));
        $bindings = new Bindings();
        $singletons = new LinkedSingletons($this->singletons);
        $factory = $this->factory->fork($modules, $bindings, $singletons);

        return new InjectorImpl($modules, $singletons, $factory, $this->lazy);
    }

    /**
     * @param array $modules
     * @param Reflection $reflection
     * @param CodeStorage $storage
     * @param LoggerInterface $logger
     * @return Injector
     */
    static public function create(
            array $modules,
            Reflection $reflection,
            CodeStorage $storage,
            LoggerInterface $logger) {
        $modules = new Modules($modules);
        $bindings = new Bindings();
        $singletons = new Singletons();
        $lazyGen = new LazyGenerator();
        $lazy = new LazyFactory($reflection, $storage, $lazyGen);

        $factory = FactoryFactory::create(
            $modules,
            $bindings,
            $singletons,
            $storage,
            $reflection
        );

        return new InjectorImpl($modules, $singletons, $factory, $lazy);
    }
}
