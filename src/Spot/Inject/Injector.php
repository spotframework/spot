<?php
namespace Spot\Inject;

interface Injector {
    /**
     * Get injected instance from the injector
     *
     * @param Key $key
     * @return mixed
     */
    function get(Key $key);

    /**
     * Alias of get(Key::ofType($typeName))
     *
     * @param string $typeName
     * @return mixed
     */
    function getInstance($typeName);

    /**
     * Get lazy proxy instance
     *
     * @param TypeKey $key
     * @return mixed
     */
    function getLazy(TypeKey $key);
    
    /**
     * Produce child injector which inherits all parent injector configurations
     * 
     * @param array $modules
     * @return Injector
     */
    function fork(array $modules);
    
    /**
     * Return modules used to configure this injector
     * 
     * @return array
     */
    function getModules();
}