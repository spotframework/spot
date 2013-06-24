<?php
namespace Spot\Module\Doctrine\MongoDB;

use IteratorAggregate;
use Doctrine\ODM\MongoDB\Query\Builder;

class DoctrineMongoQuery extends Builder implements IteratorAggregate {
    public function getIterator() {
        return $this->getQuery()->execute();
    }    
}