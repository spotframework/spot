<?php
namespace Spot\Inject\Impl\Binders;

use Spot\Inject\Bindings\ConfigCollectionBinding;
use Spot\Inject\Bindings\ConfigItemBinding;
use Spot\Inject\Bindings\ConstantNameBinding;
use Spot\Inject\Bindings\ConstantValueBinding;
use Spot\Inject\Bindings\LateBinding;
use Spot\Inject\Impl\Bindings;
use Spot\Inject\Key;
use Spot\Inject\Named;
use Symfony\Component\Yaml\Yaml;
use Spot\Inject\ConfigurationException;

class ConfigBinder {
    private $bindings;

    public function __construct(Bindings $bindings) {
        $this->bindings = $bindings;
    }

    public function bind($configFile) {
        $configs = self::read($configFile);

        $this->bindConfigCollection($configs, "", $configFile);
    }

    public function bindConfigCollection(array $configs, $prefix, $source) {
        $bindings = [];
        foreach($configs as $name => $item) {
            $bindings[$name] = $this->bindConfigItem($prefix.$name, $item, $source);
        }

        return $bindings;
    }

    public function bindConfigItem($name, $item, $source) {
        static $PATTERN = '/\{\s*?(.*?)\s*?\}/';

        $key = Key::ofConstant(Named::name($name));
        if(is_array($item)) {
            $elements = $this->bindConfigCollection($item, "{$name}.", $source);

            $binding = new ConfigCollectionBinding($name, $source, $elements);
        } else {
            if(preg_match_all($PATTERN, $item, $matches)) {
                $dependencies = [];
                foreach(preg_split($PATTERN, $item) as $constant) {
                    if($constant) {
                        $dependencies[] = new ConstantValueBinding($key, $constant);
                    }

                    $dependency = array_shift($matches[1]);
                    if($dependency) {
                        $dependencies[] = defined($dependency)
                            ? new ConstantNameBinding($key, $dependency)
                            : new LateBinding(Key::ofConstant(Named::name($dependency)));
                    }
                }

                $binding = new ConfigItemBinding($name, $source, $dependencies);
            } else {
                $binding = new ConstantValueBinding($key, $item);
            }
        }

        $this->bindings->put($binding);

        return $binding;
    }

    static public function read($configFile) {
        switch($ext = pathinfo($configFile, PATHINFO_EXTENSION)) {
            case "ini":
                return parse_ini_file($configFile, true);
            case "json":
                return json_decode(file_get_contents($configFile), true);
            case "yml":
                return Yaml::parse($configFile);
        }

        throw new ConfigurationException("Unsupported config file extension \"{$ext}\" of {$configFile}");
    }
}
