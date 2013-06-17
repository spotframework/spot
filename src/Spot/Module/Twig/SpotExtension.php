<?php
namespace Spot\Module\Twig;

use Spot\Inject\Key;
use Spot\Inject\Named;
use Spot\Inject\Injector;

class SpotExtension extends \Twig_Extension {
    private $injector;
    
    public function __construct(Injector $injector) {
        $this->injector = $injector;
    }
    
    public function getFunctions() {
        return [
            "named" => new \Twig_Function_Method($this, "named"), 
        ];
    }
    
    public function named($name) {
        return $this->injector->get(Key::ofConstant(Named::name($name)));
    }
    
    public function getName() {
        return "spot framework";
    }    
}