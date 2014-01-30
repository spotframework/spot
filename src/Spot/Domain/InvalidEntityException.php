<?php
namespace Spot\Domain;


class InvalidEntityException extends \RuntimeException
        implements \Countable, \IteratorAggregate, \ArrayAccess {
    private $entity,
            $errors;

    public function __construct($entity, array $errors) {
        parent::__construct("Entity ".get_class($entity)." with id: ".$entity->getId()." contains some invalid state");

        $this->entity = $entity;
        $this->errors = $errors;
    }

    public function getEntity() {
        return $this->entity;
    }

    public function getIterator() {
        return new \ArrayIterator($this->errors);
    }

    public function count() {
        return array_reduce($this->errors, function ($total, $error) {
            return $total + count($error);
        }, 0);
    }

    public function offsetExists($offset) {
        return isset($this->errors[$offset]);
    }

    public function offsetGet($offset) {
        return $this->errors[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->errors[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->errors[$offset]);
    }
}
