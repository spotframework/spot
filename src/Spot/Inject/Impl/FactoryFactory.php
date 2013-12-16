<?php
namespace Spot\Inject\Impl;

use Spot\Gen\CodeStorage;
use Spot\Gen\CodeWriter;
use Spot\Inject\Impl\Aspect\AspectWeaver;
use Spot\Inject\Impl\Aspect\PointCuts;
use Spot\Inject\Impl\Binders\ConfigBinder;
use Spot\Inject\Impl\Binders\JustInTimeBinder;
use Spot\Inject\Impl\Binders\ModuleBinder;
use Spot\Inject\Impl\Visitors\CircularProviderVisitor;
use Spot\Inject\Impl\Visitors\ConstantFoldingVisitor;
use Spot\Inject\Impl\Visitors\FactoryCompilerVisitor;
use Spot\Inject\Impl\Visitors\LateBindingVisitor;
use Spot\Inject\Impl\Visitors\SingletonMarkerVisitor;
use Spot\Inject\Key;
use Spot\Reflect\Reflection;

class FactoryFactory {
    private $modules,
            $locator,
            $builder,
            $storage,
            $reflection,
            $aspect;

    public function __construct(
            Modules $modules,
            BindingLocator $locator,
            BindingsBuilder $builder,
            CodeStorage $storage,
            Reflection $reflection,
            AspectWeaver $aspect) {
        $this->modules = $modules;
        $this->locator = $locator;
        $this->builder = $builder;
        $this->storage = $storage;
        $this->reflection = $reflection;
        $this->aspect = $aspect;
    }

    public function getFactory(Key $key) {
        $factory = "InjectFactory__".$this->modules->hash()."__".$key->hash();
        if($this->storage->load($factory)) {
            return $factory;
        }

        $this->builder->build();
        $binding = $this->locator->get($key);
        if(empty($binding)) {
            throw new \RuntimeException("Unable to resolve dependency of {$key}, maybe you forgot to configure it ?");
        }

        $writer = CodeWriter::create();
        $writer->writeln("use Spot\\Inject\\Key;");
        $writer->nl();
        $writer->writeln("/**");
        $writer->writeln(" * Provides {$key}");
        $writer->writeln(" * ");
        $writer->writeln(" * Configured with: ");
        array_map(function ($module) use ($writer) {
            $writer->write(" *     ");
            $writer->writeln(is_object($module) ? get_class($module) : $module);
        }, iterator_to_array($this->modules));
        $writer->writeln(" */");
        $writer->write("class {$factory} {");
        $writer->indent();
        $writer->write('static function get($s, $m, $i, $a) {');
        $writer->indent();
        $writer->write("return ");
        $binding->accept(new FactoryCompilerVisitor($writer, $this->locator, $this->aspect));
        $writer->write(";");
        $writer->outdent();
        $writer->write("}");
        $writer->outdent();
        $writer->writeln("}");

        $this->storage->store($factory, $writer);

        return $factory;
    }

    public function fork(
            Modules $modules,
            Bindings $bindings,
            LinkedSingletons $singletons) {
        return FactoryFactory::create(
            $modules,
            $bindings,
            $singletons,
            $this->storage,
            $this->reflection
        );
    }

    public function getAspect() {
        return $this->aspect;
    }

    static public function create(
            Modules $modules,
            Bindings $bindings,
            Singletons $singletons,
            CodeStorage $storage,
            Reflection $reflection) {
        $pointCuts = new PointCuts();
        $jit = new JustInTimeBinder($bindings);
        $locator = new BindingLocator($bindings, $jit, $reflection);
        $moduleBinder = new ModuleBinder($bindings, $pointCuts);
        $configBinder = new ConfigBinder($bindings);
        $binder = new Binder($moduleBinder, $configBinder, $reflection);
        $builder = new BindingsBuilder(
            $bindings,
            $modules,
            $binder,
            [
                new LateBindingVisitor($locator),
                new CircularProviderVisitor($bindings, $jit, $reflection),
                $marker = new SingletonMarkerVisitor(),
                new ConstantFoldingVisitor($bindings),
            ],
            $singletons,
            $marker
        );

        $modulesAdapter = new ModulesAdapterFactory($builder, $singletons, $storage);
        $adapter = $modulesAdapter->get($modules);
        $singletons->setSize($adapter::SINGLETONS_SIZE);

        $aspect = AspectWeaver::create($modules, $storage, $reflection, $pointCuts, $locator);

        return new FactoryFactory($modules, $locator, $builder, $storage, $reflection, $aspect);
    }
}
