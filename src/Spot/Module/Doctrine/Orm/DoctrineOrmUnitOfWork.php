<?php
namespace Spot\Module\Doctrine\Orm;

use Spot\Domain\UnitOfWork;
use Doctrine\ORM\EntityManager;

class DoctrineOrmUnitOfWork implements UnitOfWork {
    private $em;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function commit() {
        $this->em->flush();
        $this->em->commit();
    }

    public function rollback() {
        $this->em->clear();
        $this->em->rollback();
    }    
}