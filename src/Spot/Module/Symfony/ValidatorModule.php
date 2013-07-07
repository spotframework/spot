<?php
namespace Spot\Module\Symfony;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Validator\Validation;
use Spot\Module\Symfony\Impl\Validator\SpotValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

class ValidatorModule {
    /** @Provides("Symfony\Component\Validator\ConstraintValidatorFactoryInterface") */
    static function provideConstraintValidatorFactory(SpotValidatorFactory $factory) {
        return $factory;
    }
    
    /** @Provides("Symfony\Component\Validator\ValidatorInterface") */
    static function provideValidator(
            Reader $reader = null,
            ConstraintValidatorFactoryInterface $factory) {
        $builder = Validation::createValidatorBuilder();
        $builder->enableAnnotationMapping($reader);
        $builder->setConstraintValidatorFactory($factory);
        
        return $builder->getValidator();
    }
}