<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Reflect\Method;
use Spot\Aspect\Intercept\MethodInvocation;

class TerminalInvocation implements MethodInvocation {
    public $d, $k, $m, $a;
    
    public function __construct(
            $delegate,
            Method $method,
            array $arguments) {
        $this->d = $delegate;
        $this->m = $method;
        $this->a = $arguments;
    }
    
    public function getArguments() {
        return $this->a;
    }

    public function getMethod() {
        return $this->m;
    }

    public function proceed() {
        return call_user_func_array(
            [$this->d, $this->m->name],
            $this->getArguments()
        );
    }    
}