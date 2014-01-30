<?php
namespace Spot\Module\Doctrine\ORM;

use Spot\Domain\UnitOfWork;
use Doctrine\ORM\EntityManager;

class DoctrineORMUnitOfWork implements UnitOfWork {
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    function begin() {
        $this->em->beginTransaction();
    }

    function commit() {
        $this->em->flush();
        $this->em->commit();
    }

    function rollback() {
        $this->em->close();
        $this->em->rollback();
    }
}
