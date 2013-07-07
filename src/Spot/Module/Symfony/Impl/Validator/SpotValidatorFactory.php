<?php
namespace Spot\Module\Symfony\Impl\Validator;

use Spot\Inject\Injector;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;


class SpotValidatorFactory implements ConstraintValidatorFactoryInterface {
    private $injector,
            $symfonyFactory;
    
    public function __construct(
            Injector $injector,
            ConstraintValidatorFactory $symfonyFactory) {
        $this->injector = $injector;
        $this->symfonyFactory = $symfonyFactory;
    }
    
    
    public function getInstance(Constraint $constraint) {
        $class = $constraint->validatedBy();
        if(stripos($class, "Symfony\\Component\\Validator\\") === 0) {
            return $this->symfonyFactory->getInstance($constraint);
        }
        
        return $this->injector->getInstance($class);
    }    
}