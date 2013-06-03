<?php
namespace Spot\Inject\Impl;

use Spot\Inject\Impl\Binder\Config\ConfigResolver;
use Spot\Inject\Impl\Binder\Config\Configs;
use Spot\Inject\Key;
use Spot\Inject\TypeKey;
use Spot\Code\CodeStorage;
use Spot\Reflect\Reflection;
use Spot\Code\Impl\CodeWriterImpl;
use Spot\Inject\Impl\Aspect\PointCuts;
use Spot\Inject\Impl\Aspect\AspectWeaver;
use Spot\Inject\Impl\Binder\ModuleBinder;
use Spot\Inject\Impl\Binder\JustInTimeBinder;
use Spot\Inject\Impl\Binder\ConfigBinder;
use Spot\Inject\Impl\Visitor\PhpCompiler;
use Spot\Inject\Impl\Visitor\SingletonMarker;
use Spot\Inject\Impl\Visitor\BindingResolver;
use Spot\Inject\Impl\Visitor\CircularProvider;
use Spot\Inject\Impl\Visitor\LinkedBindingOptimizer;

class FactoryFactory {
    private $builder,
            $jitBinder,
            $modules,
            $bindings,
            $reflection,
            $codeStorage,
            $aspect;
    
    public function __construct(
            Bindings $bindings,
            BindingBuilder $builder,
            JustInTimeBinder $jitBinder,
            Reflection $reflection,
            CodeStorage $codeStorage,
            AspectWeaver $aspect) {
        $this->bindings = $bindings;
        $this->builder = $builder;
        $this->modules = $builder->getModules();
        $this->jitBinder = $jitBinder;
        $this->reflection = $reflection;
        $this->codeStorage = $codeStorage;
        $this->aspect = $aspect;
    }

    public function buildBindings() {
        $this->builder->build();
    }
    
    public function getFactory(Key $key) {
        $className = 'Factory__'.$this->modules->hash().'__'.$key->hash();
        
        $fqcn = $this->codeStorage->load($className);
        if(empty($fqcn)) {
            $this->buildBindings();

            $binding = $this->bindings->get($key);
            if(empty($binding) && $key instanceof TypeKey) {
                $this->jitBinder->bindNamed($key->getTypeName());
                
                $binding = $this->bindings->get($key);
            }
            
            if(empty($binding)) {
                throw new \LogicException(
                    'Unable to resolve dependency of '.$key.
                    ', maybe you forgot to configure it ?'
                );
            }
            
            $writer = new CodeWriterImpl();
            $writer->indent(3);
            
            $compiler = new PhpCompiler($writer, $this->aspect);
            $binding->accept($compiler);

            $fqcn = $this->codeStorage->store($className, 'use Spot\Inject\Key;
use Spot\Inject\Impl\InjectorImpl;
use Spot\Inject\Impl\SingletonPool;
use Spot\Inject\Impl\Modules;

/**
 * Provides '.$key.'
 * 
 * Configured with
 *     
 */
class '.$className.' {
    static function get(SingletonPool $s, InjectorImpl $i, Modules $m) {
        return '.$writer->getCode().';
    }
}');
        }
        
        return $fqcn;
    }
    
    public function link(
            Modules $modules,
            Bindings $bindings, 
            SingletonPool $singletons,
            AspectWeaver $aspect) {
        return self::create($modules, $bindings, $this->reflection, $this->codeStorage, $singletons, $aspect);
    }
    
    static public function create(
            Modules $modules,
            Bindings $bindings,
            Reflection $reflection,
            CodeStorage $codeStorage,
            SingletonPool $singletons,
            AspectWeaver $aspect) {
        $module = new ModuleBinder($reflection, $bindings, $aspect->getPointCuts());
        $jitBinder = new JustInTimeBinder($reflection, $bindings);
        $configs = new Configs();
        $resolver = new ConfigResolver($configs);
        $config = new ConfigBinder($configs, $resolver, $bindings);

        $visitors = [
            new LinkedBindingOptimizer($bindings),
            new SingletonMarker($bindings, $singletons), 
            new BindingResolver($bindings, $reflection, $jitBinder), 
            new CircularProvider($bindings, $jitBinder),
        ];
        
        $builder = new BindingBuilder($modules, $module, $config, $visitors);
        
        $modulesAdapter = new ModulesAdapterFactory($builder, $singletons, $codeStorage);
        $adapter = $modulesAdapter->getAdapterOf($modules);
        $singletons->setSize($adapter::SINGLETONS_SIZE);
        
        return new self(
            $bindings,
            $builder,
            $jitBinder,
            $reflection,
            $codeStorage,
            $aspect
        );
    }
}