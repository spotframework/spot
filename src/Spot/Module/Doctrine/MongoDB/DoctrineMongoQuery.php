<?php
namespace Spot\Module\Doctrine\MongoDB;

use Countable;
use IteratorAggregate;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\DocumentManager;

class DoctrineMongoQuery extends Builder implements Countable, IteratorAggregate {
    public function __construct(DocumentManager $dm, $documentName) {
        parent::__construct($dm, $dm->getConfiguration()->getMongoCmd(), $documentName);
    }
    
    public function count() {
        return $this->getQuery()->count();
    }
    
    public function getIterator() {
        return $this->getQuery()->execute();
    }    
}