<?php
namespace Spot\App\Web\Impl;

use Spot\Reflect\Method;
use Spot\Code\CodeStorage;
use Spot\Inject\Injector;
use Spot\Domain\DomainManager;
use Spot\Inject\Lazy;

class ActionFactory {
    private $static,
            $instance,
            $injector,
            $domain,
            $storage;
    
    public function __construct(
            StaticActionGenerator $static, 
            InstanceActionGenerator $instance,
            Injector $injector,
            /** @Lazy */DomainManager $domain,
            CodeStorage $storage) {
        $this->static = $static;
        $this->instance = $instance;
        $this->injector = $injector;
        $this->domain = $domain;
        $this->storage = $storage;
    }
    
    public function create(Method $method) {
        $className = 'Action__'.md5($method->getType()->name.'::'.$method->name);
        $fqcn = $this->storage->load($className);
        if(empty($fqcn)) {
            $code = $method->isStatic() 
                    ? $this->static->generate($className, $method)
                    : $this->instance->generate($className, $method);

            $fqcn = $this->storage->store($className, $code);
        }

        return new $fqcn($this->domain, $this->injector);
    }
}