<?php
namespace Spot\Domain;

interface Domain {
    function find($domainName, $id);

    function persist($domain);

    function remove($domain);

    function validate($domain, array $group = null);

    function newInstance($domainName, array $bindings);

    function bind($domain, array $bindings);

    function beginTransaction();

    function commit();

    function rollback();
}
