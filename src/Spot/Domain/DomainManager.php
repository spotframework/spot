<?php
namespace Spot\Domain;

interface DomainManager {
    /**
     * Create new instance of given class name, binded with given bindings
     * 
     * @param string $className
     * @param array $bindings
     * @return object
     */
    function newInstance($className, array $bindings);
    
    /**
     * Bind values into appropriate instance
     * 
     * @param object $instance
     * @param array $bindings
     */
    function bind($instance, array $bindings);

    /**
     * Retrieve domain instance with following id from persistence storage
     * 
     * @param string $className
     * @param mixed $id
     * @return null|object Domain instance from persistence storage or null if id is not exists
     */
    function find($className, $id);
    
    /**
     * Persist domain instance to persistence storage
     * 
     * @param object $instance
     */
    function persist($instance);
    
    /**
     * Remove domain instance from persistence storage
     * 
     * @param object $instance
     */
    function remove($instance);

    /**
     * Validate domain instance
     * 
     * @param object $instance
     */
    function validate($instance, $groups = null);
    
    function commit();
    
    function rollback();
    
    /**
     * Find repository of given class name
     * 
     * @param string $className
     * @return Repository
     */
    function getRepository($className);
}