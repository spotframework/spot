<?php
namespace Spot\Domain\Impl;

use Spot\Domain\Domain;
use Spot\Domain\Transactional;

class DomainImpl implements Domain {
    private $factory,
            $locator,
            $works;

    public function __construct(
            BinderFactory $factory,
            RepositoryLocator $locator,
            /** @Transactional */array $works = []) {
        $this->factory = $factory;
        $this->locator = $locator;
        $this->works = $works;
    }

    function find($domainName, $id) {
        return $this->locator->find($domainName)->find($id);
    }

    function newInstance($domainName, array $bindings) {
        $binder = $this->factory->getBinder($domainName);

        return $binder::newInstance($this, $domainName, $bindings);
    }

    function bind($domain, array $bindings) {
        $binder = $this->factory->getBinder(get_class($domain));

        return $binder::bind($this, $domain, $bindings);
    }

    function beginTransaction() {
        foreach($this->works as $work) {
            $work->begin();
        }
    }

    function commit() {
        foreach($this->works as $work) {
            $work->commit();
        }
    }

    function rollback() {
        foreach($this->works as $work) {
            $work->rollback();
        }
    }
}
