<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\App\Web\WebApp;
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