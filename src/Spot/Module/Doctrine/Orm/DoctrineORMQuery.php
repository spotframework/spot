<?php
namespace Spot\Module\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;

class DoctrineORMQuery extends QueryBuilder
        implements \IteratorAggregate, \Countable {
    public function getIterator() {
        return new \ArrayIterator($this->getQuery()->getResult());
    }

    public function count() {
        return count($this->getIterator());
    }
}
