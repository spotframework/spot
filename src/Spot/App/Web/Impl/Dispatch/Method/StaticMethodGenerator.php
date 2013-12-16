<?php
namespace Spot\App\Web\Impl\Dispatch\Method;

use Spot\Gen\CodeWriter;
use Spot\Reflect\Method;

class StaticMethodGenerator extends MethodGenerator {
    public function generateMethodCall(Method $method, CodeWriter $writer) {
        $writer->write($method->getType()->name, "::", $method->name);
    }
}
