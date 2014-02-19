<?php
namespace Spot\Module\Symfony;

use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Module\Symfony\Validator\SymfonyValidatorAdapter;
use Symfony\Component\Validator\Validation;

class SymfonyValidatorModule {
    /** @Provides("Spot\Domain\Validator", overrides = true) @Singleton */
    static public function provideValidator(SymfonyValidatorAdapter $validator) {
        return $validator;
    }

    /** @Provides("Spot\Module\Symfony\Validator\SymfonyValidatorAdapter") */
    static public function provideAdapter() {
        $symfonyValidator =
            Validation::createValidatorBuilder()
                ->enableAnnotationMapping()
                ->getValidator();

        return new SymfonyValidatorAdapter($symfonyValidator);
    }
}
