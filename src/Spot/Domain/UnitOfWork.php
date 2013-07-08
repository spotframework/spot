<?php
namespace Spot\Domain;

/**
 * Interface to implements transactional Unit of Work
 */
interface UnitOfWork {
    function begin();
    
    function commit();
    
    function rollback();
}