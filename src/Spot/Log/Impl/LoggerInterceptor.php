<?php
namespace Spot\Log\Impl;

use Spot\Reflect\Parameter;
use Psr\Log\LoggerInterface;
use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Aspect\Intercept\MethodInterceptor;

class LoggerInterceptor implements MethodInterceptor {
    private $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function intercept(MethodInvocation $invocation) {
        $args = $invocation->getArguments();
        $method = $invocation->getMethod();
        $context = array_combine(array_map(function (Parameter $parameter) {
            return $parameter->name;
        }, $method->getParameters()), $args);
        $context = array_merge([
            '__FILE__' => $method->getType()->getFileName(),
            '__LINE__' => $method->getStartLine(),
            '__CLASS__' => $method->getType()->name,
            '__FUNCTION__' => $method->name,
        ], $context);
                
        foreach($method->getAnnotations("Spot\Log\Log") as $log) {
            $this->logger->log($log->level, $log->value, $context);
        }
        
        return $invocation->proceed();
    }
}