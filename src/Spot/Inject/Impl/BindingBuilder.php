<?php
namespace Spot\Inject\Impl;

use Spot\Inject\Impl\Binder\ModuleBinder;
use Spot\Inject\Impl\Binder\ConfigBinder;

class BindingBuilder {
    private $modules,
            $module,
            $config,
            $visitors,
            $builded = false;
    
    public function __construct(
            Modules $modules,
            ModuleBinder $module,
            ConfigBinder $config,
            array $visitors) {
        $this->modules = $modules;
        $this->module = $module;
        $this->config = $config;
        $this->visitors = $visitors;
    }
    
    public function getModules() {
        return $this->modules;
    }
    
    public function isBuilded() {
        return $this->builded;
    }
    
    public function build() {
        if($this->builded) return;

        $modules = $configs = [];
        foreach($this->modules as $module) {
            if(strpos($module, '*') !== false) {
                $configs = array_merge($configs, glob($module));
            } else if(is_file($module)) {
                $configs[] = $module;
            } else {
                $modules[] = $module;
            }
        }

        $this->config->bindAll($configs);

        foreach($modules as $module) {
            $this->module->bindNamed($module);
        }
        
        foreach($this->visitors as $visitor) {
            $visitor->visit();
        }
        
        $this->builded = true;
    }
}