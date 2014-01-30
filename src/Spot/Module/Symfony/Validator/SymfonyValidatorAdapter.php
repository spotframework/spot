<?php
namespace Spot\Module\Symfony\Validator;

use Spot\Domain\InvalidEntityException;
use Spot\Domain\Validator;
use Symfony\Component\Validator\ValidatorInterface as SymfonyValidator;

class SymfonyValidatorAdapter implements Validator {
    private $symfonyValidator,
            $traverse,
            $deep;

    public function __construct(
            SymfonyValidator $symfonyValidator,
            /** @Named("symfony.validator.traverse") */$traverse = true,
            /** @Named("symfony.validator.deep") */$deep = true) {
        $this->symfonyValidator = $symfonyValidator;
        $this->traverse = $traverse;
        $this->deep = $deep;
    }


    function validate($entity, array $groups = null) {
        $violations = $this->symfonyValidator->validate(
            $entity,
            $groups,
            $this->traverse,
            $this->deep
        );
        if(count($violations) === 0) {
            return true;
        }

        $errors = [];
        foreach($violations as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        throw new InvalidEntityException($entity, $errors);
    }
}
