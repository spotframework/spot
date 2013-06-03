<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Key;
use Spot\Inject\Impl\BindingVisitor;

class InlineBinding extends AbstractBinding {
    private $className,
            $parameters;
    
    public function __construct(Key $key, $className, array $parameters) {
        parent::__construct($key);

        $this->className = $className;
        $this->parameters = $parameters;
    }
    
    public function getClass() {
        return $this->className;
    }
    
    public function getParameters() {
        return $this->parameters;
    }
    
    public function accept(BindingVisitor $visitor) {
        $visitor->visitInline($this);
    }    
}