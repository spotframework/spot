<?php
namespace Spot\App\Web\Impl;

use Spot\App\Web\View;
use Spot\App\Web\WebApp;
use Spot\Http\Request;
use Spot\Http\Response;
use Spot\Inject\Named;
use Spot\Inject\Injector;

class WebAppImpl implements WebApp {
    private $injector;
    
    public function __construct(Injector $injector) {
        $this->injector = $injector;
    }
    
    public function handle(Request $request) {        
        return $this->injector->fork([new RequestModule($request)])
            ->getInstance("Spot\App\Web\Impl\RequestHandler")
            ->handleRequest();
    }
}