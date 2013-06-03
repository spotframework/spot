<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Impl\Binding;
use Spot\Inject\Impl\BindingVisitor;

class CollectionBinding extends AbstractBinding {
    private $elements = [];

    public function add(Binding $binding) {
        $this->elements[] = $binding;
    }
    
    public function getElements() {
        return $this->elements;
    }
    
    public function accept(BindingVisitor $visitor) {
        $visitor->visitCollection($this);
    }    
}