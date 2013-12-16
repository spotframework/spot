<?php
namespace Spot\Domain;

interface UnitOfWork {
    function begin();

    function commit();

    function rollback();
}
