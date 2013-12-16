<?php
namespace Spot\Inject\Impl;

use Spot\Inject\Impl\Binders\ModuleBinder;
use Spot\Inject\Impl\Binders\ConfigBinder;
use Spot\Inject\Impl\Visitors\SingletonMarkerVisitor;
use Spot\Reflect\Reflection;

class Binder {
    private $module,
            $config,
            $reflection;

    public function __construct(
            ModuleBinder $module,
            ConfigBinder $config,
            Reflection $reflection) {
        $this->module = $module;
        $this->config = $config;
        $this->reflection = $reflection;
    }

    public function bind(Modules $modules) {
        foreach($modules as $index => $module) {
            if(is_object($module)) {
                $this->module->bind($this->reflection->get(get_class($module)), $index);
            } else if(class_exists($module)) {
                $this->module->bind($this->reflection->get($module), $index);
            } else if(is_file($module)) {
                $this->config->bind($module);
            } else if(strpos($module, "*")) {
                foreach(glob($module) as $config) {
                    if(is_file($config)) {
                        $this->config->bind($config);
                    }
                }
            }
        }
    }
}
