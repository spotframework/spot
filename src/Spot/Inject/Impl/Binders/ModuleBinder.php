<?php
namespace Spot\Inject\Impl\Binders;

use Spot\Inject\Bindings\CollectionBinding;
use Spot\Inject\Bindings\ConstantNameBinding;
use Spot\Inject\Bindings\ConstantValueBinding;
use Spot\Inject\Bindings\LateBinding;
use Spot\Inject\Bindings\LazyBinding;
use Spot\Inject\Bindings\ModuleBinding;
use Spot\Inject\Bindings\OptionalBinding;
use Spot\Inject\Bindings\ProviderMethodBinding;
use Spot\Inject\Bindings\SingletonBinding;
use Spot\Inject\ConfigurationException;
use Spot\Inject\Impl\Aspect\PointCut;
use Spot\Inject\Impl\Aspect\PointCuts;
use Spot\Inject\Impl\Bindings;
use Spot\Inject\Key;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Reflect\Type;

class ModuleBinder {
    private $bindings,
            $pointCuts;

    public function __construct(
            Bindings $bindings,
            PointCuts $pointCuts) {
        $this->bindings = $bindings;
        $this->pointCuts = $pointCuts;
    }

    public function bind(Type $module, $index) {
        foreach($module->getMethods() as $method) {
            if($method->isAnnotatedWith("Spot\\Inject\\Provides")) {
                $this->bindMethod($method, $index);
            } else if($method->isAnnotatedWith("Spot\\Inject\\Intercept")) {
                $this->bindInterceptor($method, $index);
            }
        }
    }

    public function bindInterceptor(Method $method, $index) {
        $matchers = $method->getAnnotation("Spot\\Inject\\Intercept")->getMatchers();

        $key = Key::ofType("Spot\\Aspect\\Intercept\\MethodInterceptor");
        $dependencies = [];
        foreach($method->getParameters() as $parameter) {
            $dependencies[] = $this->bindParameter($parameter);
        }
        $binding = new ProviderMethodBinding($key, $index, $method, $dependencies);

        $this->pointCuts->put(new PointCut($matchers, $binding));
    }

    public function bindMethod(Method $method, $index) {
        $key = Key::ofProvider($method);
        $provides = $method->getAnnotation("Spot\\Inject\\Provides");
        if( !$provides->overrides
            &&
            ($configured = $this->bindings->get($key))
            &&
            $configured instanceof ModuleBinding) {

            throw new ConfigurationException(
                "Binding for {$key} in ".$method->getFileName().":".$method->getStartLine().
                " is already configured in ".$configured->getSource().
                ", use @Provides(overrides = true)"
            );
        }

        $dependencies = [];
        foreach($method->getParameters() as $parameter) {
            $dependencies[$parameter->name] = $this->bindParameter($parameter);
        }

        $binding = new ProviderMethodBinding($key, $index, $method, $dependencies);
        if($method->isAnnotatedWith("Spot\\Inject\\Singleton")) {
            $binding = new SingletonBinding($binding);
        }

        if($key->isCollection()) {
            $collection = $this->bindings->get($key);
            if(empty($collection)) {
                $collection = new CollectionBinding($key);

                $this->bindings->put($collection);
            }

            $collection->addElement($binding);
        } else {
            $this->bindings->put($binding);
        }
    }

    public function bindParameter(Parameter $parameter) {
        $key = Key::ofParameter($parameter);
        if($parameter->isAnnotatedWith("Spot\\Inject\\Lazy")) {
            return new LazyBinding($key);
        }

        $binding = new LateBinding($key);
        if($parameter->isDefaultValueAvailable()) {
            $binding = new OptionalBinding(
                $key,
                $parameter->isDefaultValueConstant()
                    ? new ConstantNameBinding($key, $parameter->getDefaultValueConstantName())
                    : new ConstantValueBinding($key, $parameter->getDefaultValue())
            );
        }

        return $binding;
    }
}
