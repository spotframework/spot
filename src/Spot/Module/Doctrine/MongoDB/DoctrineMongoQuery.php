<?php
namespace Spot\Module\Doctrine\MongoDB;

use Countable;
use IteratorAggregate;
use Doctrine\ODM\MongoDB\Query\Builder;

class DoctrineMongoQuery extends Builder implements Countable, IteratorAggregate {
    public function count() {
        return $this->getQuery()->count();
    }
    
    public function getIterator() {
        return $this->getQuery()->execute();
    }    
}