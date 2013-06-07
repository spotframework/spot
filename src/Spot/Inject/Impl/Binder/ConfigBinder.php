<?php
namespace Spot\Inject\Impl\Binder;

use Spot\Inject\Impl\Binder\Config\ConfigItem;
use Spot\Inject\Impl\Binder\Config\Configs;
use Spot\Inject\Impl\Binder\Config\ConfigResolver;
use Spot\Inject\Impl\Binding\CollectionBinding;
use Spot\Inject\Impl\Binding\ConstantBinding;
use Spot\Inject\Impl\Bindings;
use Spot\Inject\Key;
use Spot\Inject\Named;

class ConfigBinder {
    private $configs,
            $resolver,
            $bindings,
            $collections = [];
    
    public function __construct(
            Configs $configs,
            ConfigResolver $resolver,
            Bindings $bindings) {
        $this->configs = $configs;
        $this->resolver = $resolver;
        $this->bindings = $bindings;
    }

    public function bindAll(array $files) {
        foreach($files as $file) {
            switch(pathinfo($file, PATHINFO_EXTENSION)) {
                case 'ini':
                    $configs = parse_ini_file($file, true);

                    $this->bind($configs, "", $file);
                    break;
                case 'json':
                    $json = file_get_contents($file);
                    $configs = json_decode($json, true);

                    $this->bind($configs, "", $file);
                    break;
            }
        }

        $this->resolver->resolve();
        foreach($this->configs as $name => $item) {
            $value = $item->getValue();
            $key = Key::ofConstant(Named::name($name));

            $this->bindings->put($key, new ConstantBinding($key, $value));
        }

        foreach($this->collections as $name => $indexes) {
            $key = Key::ofConstant(Named::name($name));

            $collection = [];
            foreach($indexes as $index) {
                $binding = $this->bindings->get(Key::ofConstant(Named::name($name.'.'.$index)));

                $collection[$index] = $binding->getValue();
            }

            $this->bindings->put($key, new ConstantBinding($key, $collection));
        }
    }
    
    public function bind($configs, $prefix, $source) {
        foreach($configs as $name => $value) {
            if($this->configs->get($prefix.$name)) {
                $item = $this->configs->get($prefix.$name);

                throw new \LogicException(
                    "Configuration \"{$prefix}{$name}\" defined in {$source} ".
                    "is already defined in ".$item->getSource()
                );
            }

            if(is_array($value)) {
                $this->bind($value, $prefix.$name.".", $source);

                $this->collections[$prefix.$name] = array_keys($value);
            } else {
                $this->configs->put($prefix.$name, new ConfigItem($value, $source));
            }
        }
    }
}