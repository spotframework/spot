<?php
namespace Spot\Module\Doctrine\ORM;

use Spot\Domain\Repository;
use Doctrine\ORM\EntityManager;

abstract class DoctrineORMRepository implements Repository {
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    function all() {
        $className = static::repositoryOf();
        $alias = strtolower($className[strrpos($className, "\\") + 1]);

        return (new DoctrineORMQuery($this->em))
            ->addSelect($alias)
            ->from($className, $alias);
    }

    function find($id) {
        return $this->em->find(static::repositoryOf(), $id);
    }

    function persist($entity) {
        $this->em->persist($entity);
    }

    function remove($entity) {
        $this->em->remove($entity);
    }
}
