<?php
namespace Spot\Domain;

interface Repository {
    /**
     * Retrieve all entities from this repository
     */
    function all();
    
    /**
     * Retrieve entity with following id
     * 
     * @param type $id
     */
    function find($id);
    
    /**
     * Mark entity to be persisted
     * 
     * @param object $instance
     */
    function persist($entity);
    
    /**
     * Mark entity to be removed
     * 
     * @param object $entity
     */
    function remove($entity);
    
    /**
     * Get entity class name
     * 
     * @return string
     */
    static function repositoryOf();
}