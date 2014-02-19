<?php
namespace Spot\App\Web\Impl\Dispatch;

use Spot\App\Web\Impl\Dispatch\Method\MethodFactory;
use Spot\App\Web\Request;
use Spot\App\Web\Response;
use Spot\Domain\Domain;
use Spot\Inject\Injector;
use Spot\Inject\Lazy;

class MethodDispatcher {
    private $domain,
            $injector,
            $factory;

    public function __construct(
            Domain $domain,
            Injector $injector,
            MethodFactory $factory) {
        $this->domain = $domain;
        $this->injector = $injector;
        $this->factory = $factory;
    }

    public function dispatch(array $action, Request $request, Response $response) {
        $adapter = $this->factory->get($action);

        return $adapter::invoke(
            $this->injector,
            $this->domain,
            $request,
            $response
        );
    }
}
