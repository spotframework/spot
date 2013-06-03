<?php
namespace Spot\App\Web\Impl;

interface MappingProvider {
    /**
     * @return array<RouteMapping>
     */
    function getMappings();
}