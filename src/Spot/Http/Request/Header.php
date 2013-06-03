<?php
namespace Spot\Http\Request;

use ArrayIterator;

class Header extends Vars {
    public function offsetGet($index) {
        return \ArrayObject::offsetGet("HTTP_$index");
    }

    public function offsetExists($index) {
        return parent::offsetExists("HTTP_$index");
    }

    public function getIterator() {
        $headers = [];
        foreach(parent::getIterator() as $name => $value) {
            if(strpos($name, "HTTP_") === 0) {
                if($name === "HTTP_USER_AGENT") {
                    $headers["USER_AGENT"] = new UserAgent($value);
                } else {
                    $headers[substr($name, 5)] = $value;
                }
            }
        }

        return new ArrayIterator($headers);
    }
}