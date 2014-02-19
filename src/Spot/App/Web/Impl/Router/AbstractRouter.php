<?php
namespace Spot\App\Web\Impl\Router;

use Spot\App\Web\Router;

abstract class AbstractRouter implements Router {
    private $names;

    public function __construct(array $names) {
        $this->names = $names;
    }

    public function generate($name, array $params = []) {
        if(isset($this->names[$name])) {
            if(count($params) !== count($this->names[$name]["params"])) {
                throw new \InvalidArgumentException(
                    "Route \"{$name}\" requires ".
                        count($this->names[$name]["params"]).
                        " parameters, ".count($params)." given"
                );
            }

            if(key($params) === 0) {
                $params = array_combine(
                    $this->names[$name]["params"],
                    $params
                );
            }

            return str_replace(array_keys($params), $params, $this->names[$name]["uri"]);
        }
    }
}
