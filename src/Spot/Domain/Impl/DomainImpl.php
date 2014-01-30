<?php
namespace Spot\Domain\Impl;

use Spot\Domain\Domain;
use Spot\Domain\Transactional;
use Spot\Domain\Validator;

class DomainImpl implements Domain {
    private $factory,
            $locator,
            $validator,
            $works;

    public function __construct(
            BinderFactory $factory,
            RepositoryLocator $locator,
            Validator $validator,
            /** @Transactional */array $works = []) {
        $this->factory = $factory;
        $this->locator = $locator;
        $this->validator = $validator;
        $this->works = $works;
    }

    function find($domainName, $id) {
        return $this->locator->find($domainName)->find($id);
    }

    function persist($domain) {
        return $this->locator->find(get_class($domain))->persist($domain);
    }

    function remove($domain) {
        return $this->locator->find(get_class($domain))->remove($domain);
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

    function validate($domain, array $groups = null) {
        return $this->validator->validate($domain, $groups);
    }
}
