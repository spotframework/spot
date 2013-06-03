<?php
namespace Spot\Domain\Impl;

use Spot\Domain\DomainManager;
use Spot\Domain\Transactional;

class ManagerImpl implements DomainManager {
    private $factory,
            $finder,
            $works = [];
    
    public function __construct(
            BinderFactory $factory,
            RepositoryFinder $finder,
            /** @Transactional */array $works = []) {
        $this->factory = $factory;
        $this->finder = $finder;
        $this->works = $works;
    }
    
    public function newInstance($className, array $bindings) {
        $binderClass = $this->factory->getBinder($className);
        $binder = new $binderClass($this);
        
        return $binder->newInstance($bindings);
    }
    
    public function bind($instance, array $bindings) {
        $binderClass = $this->factory->getBinder(get_class($instance));
        
        (new $binderClass($this))->bind($instance, $bindings);
    }

    public function find($className, $id) {
        return $this->getRepository($className)->find($id);
    }

    public function persist($instance) {
        $this->getRepository(get_class($instance))->persist($instance);
    }

    public function remove($instance) {
        $this->getRepository(get_class($instance))->remove($instance);
    }

    public function validate($instance, $groups = null) {
        return $this->validator->validate($instance, $groups);
    }

    public function getRepository($className) {
        return $this->finder->get($className);
    }

    public function commit() {
        foreach($this->works as $work) {
            $work->commit();
        }
    }

    public function rollback() {
        foreach($this->works as $work) {
            $work->rollback();
        }
    } 
}