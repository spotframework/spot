<?php
namespace Spot\Module\Symfony;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Symfony\Component\Validator\Validation;
use Doctrine\Common\Annotations\Reader;

class ValidatorModule {
    /** @Provides("Symfony\Component\Validator\ValidatorInterface") */
    static function provideValidator(Reader $reader = null) {
        $builder = Validation::createValidatorBuilder();
        $builder->enableAnnotationMapping($reader);
        
        return $builder->getValidator();
    }
}