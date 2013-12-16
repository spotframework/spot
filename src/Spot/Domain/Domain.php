<?php
namespace Spot\Domain;

interface Domain {
    function find($domainName, $id);

    function newInstance($domainName, array $bindings);

    function bind($domain, array $bindings);

    function beginTransaction();

    function commit();

    function rollback();
}
