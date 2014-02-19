<?php
namespace Spot\Inject;

interface Injector {
    /**
     * Get injected instance represented by given key
     *
     * @param Key $key
     * @return mixed
     */
    function get(Key $key);

    /**
     * Alias of get(Key::ofType($type))
     *
     * @param string $type
     * @return mixed
     */
    function getInstance($type);


    /**
     * Get lazy proxy instance represented by given key
     * that will be initialized once it used
     *
     * @param Key $key
     * @return mixed
     */
    function getLazy(Key $key);

    /**
     * Create a child injector which inherits bindings from this injector
     *
     * @param array $modules
     * @return Injector
     */
    function fork(array $modules);
}
