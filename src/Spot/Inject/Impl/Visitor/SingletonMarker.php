<?php
namespace Spot\Inject\Impl\Visitor;

use Spot\Inject\Impl\Bindings;
use Spot\Inject\Impl\SingletonPool;
use Spot\Inject\Impl\Binding\InstanceBinding;
use Spot\Inject\Impl\Binding\SingletonBinding;
use Spot\Inject\Impl\Binding\CollectionBinding;

class SingletonMarker extends AbstractVisitor {
    private $singletons,
            $count;
    
    public function __construct(
            Bindings $bindings, 
            SingletonPool $singletons) {
        parent::__construct($bindings);
        
        $this->singletons = $singletons;
        $this->count = $bindings->getInstanceSize();
    }
    
    public function visit() {        
        parent::visit();
        
        $this->singletons->setSize($this->count);
    }
    
    public function getCount() {
        return $this->count;
    }
    
    public function visitCollection(CollectionBinding $binding) {
        foreach($binding->getElements() as $element) {
            $element->accept($this);
        }
    }
    
    public function visitSingleton(SingletonBinding $binding) {
        $binding->setIndex($this->count);
        
        ++$this->count;
    }
}