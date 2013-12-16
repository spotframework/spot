<?php
namespace Spot\Gen;

class CodeStorage {
    private $dir;

    protected function __construct($dir) {
        $this->dir = rtrim($dir, "/")."/";
    }

    /**
     * Get generated code of given class name from this storage,
     * and load it to the current runtime
     *
     * @param string $typeName
     * @return bool true on success, false on failure
     */
    function load($typeName) {
        if(class_exists($typeName, false)) {
            return true;
        }

        $path = $this->dir . $typeName . ".php";
        if(is_file($path)) {
            require $path;

            return true;
        }

        return false;
    }

    /**
     * Store generated code to this storage, and immediately load it to
     * current runtime
     *
     * @param string $typeName
     * @param CodeWriter $code
     * @return bool true on success, false on failure
     */
    function store($typeName, CodeWriter $code) {
        $path = $this->dir . $typeName . ".php";
        if(!is_dir($this->dir)) {
            mkdir($this->dir, 0777, true);
        }

        if(!is_writable($this->dir)) {
            chmod($this->dir, 0777);
        }

        file_put_contents($path, "<?php " . PHP_EOL . $code, LOCK_EX);

        return $this->load($typeName);
    }

    static public function create($dir) {
        return new CodeStorage($dir);
    }
}
