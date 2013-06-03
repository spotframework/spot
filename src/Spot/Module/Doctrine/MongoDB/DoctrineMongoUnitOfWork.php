<?php
namespace Spot\Module\Doctrine\MongoDB;

use Spot\Domain\UnitOfWork;
use Doctrine\ODM\MongoDB\DocumentManager;

class DoctrineMongoUnitOfWork implements UnitOfWork {
    private $dm;
    
    public function __construct(DocumentManager $dm) {
        $this->dm = $dm;
    }
    
    public function commit() {
        $this->dm->flush();
    }

    public function rollback() {
        $this->dm->clear();
    }    
}