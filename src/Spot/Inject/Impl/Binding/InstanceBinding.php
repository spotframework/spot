<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Key;
use Spot\Inject\Impl\BindingVisitor;

class InstanceBinding extends AbstractBinding {
    private $index;
    
    public function __construct(Key $key, $index) {
        parent::__construct($key);

        $this->index = $index;
    }
    
    public function getIndex() {
        return $this->index;
    }
    
    public function accept(BindingVisitor $visitor) {
        $visitor->visitInstance($this);
    }    
}