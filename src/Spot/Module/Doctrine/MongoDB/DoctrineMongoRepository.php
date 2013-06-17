<?php
namespace Spot\Module\Doctrine\MongoDB;

use Spot\Domain\Repository;
use Doctrine\ODM\MongoDB\DocumentManager;

abstract class DoctrineMongoRepository implements Repository {
    private $dm;
    
    public function __construct(DocumentManager $dm) {
        $this->dm = $dm;
    }
    
    public function all() {
        
    }

    public function find($id) {
        return $this->dm->find(static::repositoryOf(), $id);
    }

    public function persist($entity) {
        $this->dm->persist($entity);
    }

    public function remove($entity) {
        $this->dm->remove($entity);
    }
}