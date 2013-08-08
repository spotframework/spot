<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\Http\Response;
use Spot\App\Web\WebApp;
use Spot\App\Web\Impl\RequestModule;

class WebAppImpl implements WebApp {
    private $injectorFactory,
            $modules;
    
    public function __construct(
            callable $injectorFactory,
            array $modules) {
        $this->injectorFactory = $injectorFactory;
        $this->modules = $modules;
    }
    
    public function handle(Request $request) {        
        return call_user_func_array($this->injectorFactory, array_merge(
                $this->modules,
                [new RequestModule($request, new Response())]
            ))
            ->getInstance("Spot\App\Web\Impl\RequestHandler")
            ->handleRequest();
    }
}