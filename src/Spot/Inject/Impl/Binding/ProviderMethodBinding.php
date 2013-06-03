<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Key;
use Spot\Reflect\Method;
use Spot\Inject\Impl\BindingVisitor;

class ProviderMethodBinding extends AbstractBinding {
    private $method,
            $parameters;
    
    public function __construct(
            Key $key,
            Method $method,
            array $parameters) {
        parent::__construct($key);

        $this->method = $method;
        $this->parameters = $parameters;
    }
    
    /**
     * @return Method
     */
    public function getMethod() {
        return $this->method;
    }
    
    public function getModule() {
        return $this->method->getType()->name;
    }
    
    public function getProvider() {
        return $this->method->name;
    }
    
    public function getParameters() {
        return $this->parameters;
    }
    
    public function accept(BindingVisitor $visitor) {
        $visitor->visitProviderMethod($this);
    }
}