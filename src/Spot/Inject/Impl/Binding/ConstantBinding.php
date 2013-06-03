<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Key;
use Spot\Inject\Impl\BindingVisitor;

class ConstantBinding extends AbstractBinding {
    private $value;
    
    public function __construct(Key $key, $value) {
        parent::__construct($key);

        $this->value = $value;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function accept(BindingVisitor $visitor) {
        $visitor->visitConstant($this);
    }    
}