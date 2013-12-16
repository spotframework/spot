<?php
namespace Spot\Inject\Impl\Visitors;

use Spot\Inject\Bindings\LateBinding;
use Spot\Inject\Bindings\ProviderMethodBinding;
use Spot\Inject\ConfigurationException;
use Spot\Inject\Impl\Binders\JustInTimeBinder;
use Spot\Inject\Impl\Bindings;
use Spot\Reflect\Reflection;

class CircularProviderVisitor extends AbstractVisitor {
    private $stack = [],
            $bindings,
            $jitBinder,
            $reflection;

    public function __construct(
            Bindings $bindings,
            JustInTimeBinder $jitBinder,
            Reflection $reflection) {
        $this->bindings = $bindings;
        $this->jitBinder = $jitBinder;
        $this->reflection = $reflection;
    }

    public function visitProviderMethod(ProviderMethodBinding $providerMethod) {
        if(in_array($providerMethod, $this->stack)) {
            throw new ConfigurationException("Circular dependency detected");
        }

        array_push($this->stack, $providerMethod);
        $dependencies = $providerMethod->getDependencies();
        foreach($dependencies as $name => $dependency) {
            if($dependency instanceof LateBinding) {
                try {
                    $dependency->accept($this);
                } catch(ConfigurationException $e) {
                    $key = $dependency->getKey();

                    try {
                        $this->jitBinder->bind($this->reflection->get($key->getType()));
                    } catch(ConfigurationException $e){
                        $method = $providerMethod->getMethod();
                        throw new ConfigurationException(
                            $e->getMessage(). ", required by parameter \$".
                            "{$name} in {$method->class}::{$method->name}()"

                        );
                    }

                    $dependencies[$name] = $this->bindings->get($key);

                    $providerMethod = new ProviderMethodBinding(
                        $key,
                        $providerMethod->getIndex(),
                        $providerMethod->getMethod(),
                        $dependencies
                    );

                    $this->bindings->put($providerMethod);
                }
            }
        }
        array_pop($this->stack);
    }

    public function visitLate(LateBinding $late) {
        $this->bindings->get($late->getKey())->accept($this);
    }
}
