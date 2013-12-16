<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Gen\CodeStorage;
use Spot\Gen\CodeWriter;
use Spot\Inject\Impl\BindingLocator;
use Spot\Inject\Impl\Modules;
use Spot\Inject\Impl\Singletons;
use Spot\Inject\Injector;
use Spot\Reflect\Method;
use Spot\Reflect\Reflection;
use Spot\Reflect\Type;

class AspectWeaver {
    private $modules,
            $storage,
            $reflection,
            $proxyGen;

    public function __construct(
            Modules $modules,
            CodeStorage $storage,
            Reflection $reflection,
            ProxyGenerator $proxyGen,
            PointCuts $pointCuts) {
        $this->modules = $modules;
        $this->storage = $storage;
        $this->reflection = $reflection;
        $this->proxyGen = $proxyGen;
        $this->pointCuts = $pointCuts;
    }

    public function get($className, $delegate, Singletons $singletons) {
        $proxy = "AspectProxy__".$this->modules->hash()."__".md5($className);
        if(!$this->storage->load($proxy)) {
            $writer = CodeWriter::create();

            $writer->writeln("use Spot\\Inject\\Impl\\Aspect\\DelegateInvocation;");
            $writer->writeln("use Spot\\Inject\\Impl\\Aspect\\TerminalInvocation;");

            $writer->write("class ", $proxy, " extends ", $className, " {");
            $writer->indent();
            $this->proxyGen->generate($this->reflection->get($className), $writer);
            $writer->outdent();
            $writer->write("}");

            $this->storage->store($proxy, $writer);
        }

        return new $proxy($this->reflection, $delegate, $singletons);
    }

    public function check(Type $type) {
        foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
            if($this->pointCuts->matches($method)) {
                return true;
            }
        }

        return false;
    }

    static public function create(
            Modules $modules,
            CodeStorage $storage,
            Reflection $reflection,
            PointCuts $pointCuts,
            BindingLocator $locator) {
        $proxyGen = new ProxyGenerator($pointCuts, $locator);
        $aspect = new AspectWeaver($modules, $storage, $reflection, $proxyGen, $pointCuts);
        $proxyGen->setAspectWeaver($aspect);

        return $aspect;
    }
}
