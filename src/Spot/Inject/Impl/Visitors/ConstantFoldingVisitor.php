<?php
namespace Spot\Inject\Impl\Visitors;

use Spot\Inject\Binding;
use Spot\Inject\Bindings\ConfigItemBinding;
use Spot\Inject\Impl\Bindings;
use Spot\Inject\Impl\Visitors\AbstractVisitor;
use Spot\Inject\Bindings\ConstantValueBinding;
use Spot\Inject\Bindings\LateBinding;
use Spot\Inject\Bindings\ConfigCollectionBinding;

class ConstantFoldingVisitor extends AbstractVisitor {
    private $bindings;

    public function __construct(Bindings $bindings) {
        $this->bindings = $bindings;
    }

    public function visitConfigCollection(ConfigCollectionBinding $configCollection) {
        $elements = $configCollection->getElements();
        foreach($elements as $name => $element) {
            if($element instanceof ConfigItemBinding) {
                $elements[$name] = $this->visitConfigItem($element);
            }
        }

        $optimized = new ConfigCollectionBinding(
            $configCollection->getName(),
            $configCollection->getSource(),
            $elements
        );

        $this->bindings->put($optimized);
    }

    public function visitConfigItem(ConfigItemBinding $configItem) {
        $dependencies = $this->optimize($configItem->getDependencies());
        if(count($dependencies) == 1) {
            $optimized = new ConstantValueBinding(
                $configItem->getKey(),
                reset($dependencies)->getValue()
            );
        } else {
            $optimized = new ConfigItemBinding($configItem->getName(), $configItem->getSource(), $dependencies);
        }

        $this->bindings->put($optimized);

        return $optimized;
    }

    public function optimize(array $dependencies) {
        for($i = 0, $len = count($dependencies) - 1; $i < $len; ++$i) {
            $first = $this->resolve($dependencies[$i]);
            $second = $this->resolve($dependencies[$i + 1]);
            if($this->match($first, $second)) {
                $folded = $this->fold($first, $second);

                return $this->optimize(array_merge(
                    array_slice($dependencies, 0, $i),
                    [$folded],
                    array_slice($dependencies, $i + 2)
                ));
            }
        }

        return $dependencies;
    }

    public function fold(ConstantValueBinding $first, ConstantValueBinding $second) {
        return new ConstantValueBinding(
            $first->getKey(),
            $first->getValue().$second->getValue()
        );
    }

    public function match(Binding $first, Binding $second) {
        return $first instanceof ConstantValueBinding && $second instanceof ConstantValueBinding;
    }

    public function resolve(Binding $binding) {
        if($binding instanceof LateBinding) {
            return $this->bindings->get($binding->getKey());
        }

        return $binding;
    }
}
