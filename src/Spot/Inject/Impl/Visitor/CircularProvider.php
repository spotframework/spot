<?php
namespace Spot\Inject\Impl\Visitor;

use Spot\Inject\Impl\Bindings;
use Spot\Inject\Impl\Binder\JustInTimeBinder;
use Spot\Inject\Impl\Binding\SingletonBinding;
use Spot\Inject\Impl\Binding\UnresolvedBinding;
use Spot\Inject\Impl\Binding\ProviderMethodBinding;

class CircularProvider extends AbstractVisitor {
    private $bindings,
            $jit,
            $stack = [];
    
    public function __construct(
            Bindings $bindings, 
            JustInTimeBinder $jit) {
        parent::__construct($bindings);
        $this->bindings = $bindings;
        $this->jit = $jit;
    }
    
    public function visitSingleton(SingletonBinding $binding) {
        $binding->delegateAccept($this);
    }
    
    public function visitUnresolved(UnresolvedBinding $binding) {
        $binding->delegateAccept($this);
    }
    
    public function visitProviderMethod(ProviderMethodBinding $binding) {
        if(in_array($binding, $this->stack)) {
            throw new \Exception();
        }
        
        array_push($this->stack, $binding);
        foreach($binding->getParameters() as $parameter) {
            if($parameter instanceof UnresolvedBinding) {
                try {
                    $parameter->accept($this);
                } catch(\Exception $e) {
                    $key = $parameter->getKey();
                    $delegate = $parameter->getDelegate();
                    $this->jit->bindNamed($key->getTypeName());
                    
                    $inline = $this->bindings->get($key);

                    $parameter->setDelegate($inline);
                    
                    $this->bindings->put($key, $delegate);
                }
            }
        }
        array_pop($this->stack);
    }
}