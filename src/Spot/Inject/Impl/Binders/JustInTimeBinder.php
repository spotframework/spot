<?php
namespace Spot\Inject\Impl\Binders;

use Spot\Inject\Bindings\ConstantNameBinding;
use Spot\Inject\Bindings\ConstantValueBinding;
use Spot\Inject\Bindings\InlineBinding;
use Spot\Inject\ConfigurationException;
use Spot\Inject\Impl\Bindings;
use Spot\Inject\Key;
use Spot\Reflect\Parameter;
use Spot\Reflect\Reflection;
use Spot\Reflect\Type;

class JustInTimeBinder {
    private $bindings;

    public function __construct(Bindings $bindings) {
        $this->bindings = $bindings;
    }

    public function bind(Type $type) {
        if(!$type->isInstantiable()) {
            throw new ConfigurationException("Type {$type->name} is not instantiable");
        }

        $dependencies = [];
        $ctor = $type->getConstructor();
        foreach($ctor ? $ctor->getParameters() : [] as $parameter) {
            $dependencies[] = $this->bindParameter($parameter);
        }

        $this->bindings->put(new InlineBinding($type, $dependencies));
    }

    public function bindParameter(Parameter $parameter) {
        $key = Key::ofParameter($parameter);
        $binding = $this->bindings->get($key);
        if(empty($binding)) {
            $class = $parameter->getClass();
            if($class && !$parameter->isDefaultValueAvailable()) {
                try {
                    $this->bind($class);
                } catch(ConfigurationException $e) {
                    throw new ConfigurationException(
                        $e->getMessage().", required by parameter \${$parameter->name} in ".
                        $parameter->getDeclaringClass()->name."::".$parameter->getDeclaringFunction()->name
                    );
                }

                $binding = $this->bindings->get($key);
            }
        }

        if(empty($binding) && $parameter->isDefaultValueAvailable()) {
            $binding = $parameter->isDefaultValueConstant()
                ? new ConstantNameBinding($key, $parameter->getDefaultValueConstantName())
                : new ConstantValueBinding($key, $parameter->getDefaultValue());
        }

        if(empty($binding)) {
            throw new ConfigurationException(
                "Missing dependency {$key}".
                ", required by parameter \${$parameter->name} in ".
                $parameter->getMethod()->getType()->name."::".$parameter->getMethod()->name."()"
            );
        }

        return $binding;
    }
}
