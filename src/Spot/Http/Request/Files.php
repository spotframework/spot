<?php
namespace Spot\Http\Request;

class Files extends Vars {
    static public function normalize(array $_files) {
        foreach ($_files as $i => $entry) {            
            if(isset($entry["name"]) && is_array($entry["name"])) {
                $files = [];
                foreach($entry["name"] as $k => $name) {
                    $files[$k] = new File([
                        "name" => $name,
                        "tmp_name" => $entry["tmp_name"][$k],
                        "size" => $entry["size"][$k],
                        "type" => $entry["type"][$k],
                        "error" => $entry["error"][$k]
                    ], \ArrayObject::ARRAY_AS_PROPS);
                }
                
                $_files[$i] = self::normalize($files);
            } else {
                if ($entry instanceof \ArrayObject) {
                    $entry = $entry->getArrayCopy();
                }
                
                $_files[$i] = new File($entry, \ArrayObject::ARRAY_AS_PROPS);
            }
        }
        
        return $_files;
    }
}