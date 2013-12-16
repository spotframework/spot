<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Reflect\Reflection;

class TerminalInvocation implements MethodInvocation {
    public $c, $m, $a, $d, $r;

    public function __construct(array $args, $class, $method, $delegate, Reflection $reflection) {
        $this->a = $args;
        $this->c = $class;
        $this->m = $method;
        $this->d = $delegate;
        $this->r = $reflection;
    }

    function getArguments() {
        return $this->a;
    }

    function proceed() {
        switch(count($this->a)) {
            case 0:
                return $this->d->{$this->m}();
            case 1:
                return $this->d->{$this->m}($this->a[0]);
            case 2:
                return $this->d->{$this->m}($this->a[0], $this->a[1]);
            case 3:
                return $this->d->{$this->m}($this->a[0], $this->a[1], $this->a[2]);
            default:
                return call_user_func_array(
                    [$this->d, $this->m],
                    $this->a
                );
        }
    }

    function getMethod() {
        return $this->r->get($this->c)->getMethod($this->m);
    }
}
