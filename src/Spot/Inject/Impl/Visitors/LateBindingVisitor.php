<?php
namespace Spot\Inject\Impl\Visitors;

use Spot\Inject\Bindings\ConfigCollectionBinding;
use Spot\Inject\Bindings\ConfigItemBinding;
use Spot\Inject\Bindings\InlineBinding;
use Spot\Inject\Bindings\LateBinding;
use Spot\Inject\Bindings\ProviderMethodBinding;
use Spot\Inject\ConfigurationException;
use Spot\Inject\Impl\BindingLocator;

class LateBindingVisitor extends AbstractVisitor {
    private $locator;

    public function __construct(BindingLocator $locator) {
        $this->locator = $locator;
    }

    public function visitConfigItem(ConfigItemBinding $configItem) {
        foreach($configItem->getDependencies() as $dependency) {
            try {
                $dependency->accept($this);
            } catch(ConfigurationException $e) {
                throw new ConfigurationException(
                    $e->getMessage().
                    ", required by config ".$configItem->getKey()->getQualifier().
                    " in ".$configItem->getSource()
                );
            }
        }
    }

    public function visitProviderMethod(ProviderMethodBinding $binding) {
        $method = $binding->getMethod();
        foreach($binding->getDependencies() as $name => $dependency) {
            try {
                $dependency->accept($this);
            } catch(ConfigurationException $e) {
                throw new ConfigurationException(
                    $e->getMessage().
                    ", required by parameter \$".$name." in ".
                    $method->getType()->name."::".$method->name
                );
            }
        }
    }

    public function visitLate(LateBinding $late) {
        $key = $late->getKey();
        $resolution = $this->locator->get($key);
        if(empty($resolution)) {
            throw new ConfigurationException("Missing binding for {$key}");
        }
    }
}
