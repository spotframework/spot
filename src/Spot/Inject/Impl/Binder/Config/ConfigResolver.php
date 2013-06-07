<?php
namespace Spot\Inject\Impl\Binder\Config;

class ConfigResolver {
    private $configs;

    public function __construct(Configs $configs) {
        $this->configs = $configs;
    }

    public function resolve() {
        foreach($this->configs as $name => $item) {
            $value = $item->getValue();
            if(is_array($value)) continue;
            if(strpos($value, '{') === false) continue;

            $this->resolveItem($name, $item);
        }
    }

    public function resolveItem($name, ConfigItem $item) {
        $value = $item->getValue();
        if(!preg_match_all('/\{\s*([\w|_|\d|\.]+)\s*\}/', $value, $matches)) {
            return $item;
        }

        $literals = $matches[0];
        $placeholders = $matches[1];
        $resolves = [];
        foreach($placeholders as $i => $placeholder) {
            if($placeholder === "__DIR__") {
                $resolves[$i] = dirname($item->getSource());
            } else if($placeholder === "__FILE__") {
                $resolves[$i] = $item->getSource();
            } else if(($resolved = $this->configs->get($placeholder))) {
                $this->resolveItem($placeholder, $resolved);
                $item = $this->configs->get($placeholder);
                
                $resolves[$i] = $item->getValue();
            } else {
                throw new \LogicException(
                    "Unable to find config with key \"{$placeholder}\", ".
                    "required by key \"{$name}\" in ".$item->getSource()
                );
            }
        }

        $this->configs->put(
            $name,
            new ConfigItem(str_replace($literals, $resolves, $value), $item->getSource())
        );
    }
}