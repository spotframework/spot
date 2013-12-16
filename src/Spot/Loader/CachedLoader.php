<?php
namespace Spot\Loader;

use Spot\Gen\CodeStorage;
use Spot\Gen\CodeWriter;
use Spot\Reflect\Reflection;
use Composer\Autoload\ClassLoader as ComposerLoader;

class CachedLoader {
    private $composer,
            $rootNamespaces = [],
            $dependencies = [];

    public function __construct(ComposerLoader $composer, $dump = null) {
        $this->composer = $composer;
        $this->dump = rtrim($dump ?: sys_get_temp_dir(), "/");

        spl_autoload_register([$this, "load"], true, true);
    }

    public function __destruct() {
        if(!file_exists($this->dump)) {
            mkdir($this->dump);
        }

        foreach($this->dependencies as $ns => $classes) {
            $this->write($ns, $classes);
        }
    }

    public function write($ns, array $classes) {
        $filename = $this->dump."/".md5($ns).".php.cache";

        $file = fopen($filename, "a");

        fputs($file, "<?php ".PHP_EOL);
        foreach($classes as $class) {
            $type = new \ReflectionClass($class);
            $source = file_get_contents($type->getFileName());
            $source = preg_replace(
                '/\<\?php\s+namespace[ ]+(.*?)\;/',
                'namespace $1{',
                $source
            )."}";

            fputs($file, $source);
        }
        fputs($file, PHP_EOL."?>".PHP_EOL);

        fclose($file);
    }

    public function loadCache($rootClass) {
        $ns = substr($rootClass, 0, strpos($rootClass, "\\"));
        $filename = $this->dump."/".md5($ns).".php.cache";
        if(is_file($filename)) {
            require $filename;

            return true;
        }

        return false;
    }

    public function addRootClass($rootClass) {
        $this->rootNamespaces[$rootClass] =
            substr($rootClass, 0, strrpos($rootClass, "\\"))
            ?:
            substr($rootClass, 0, strrpos($rootClass, "_"));

        return $this;
    }

    public function load($class) {
        $found = $this->composer->loadClass($class);
        if(isset($this->rootNamespaces[$class])) {
            $this->loadCache($class);
        } else if($found) {
            foreach($this->rootNamespaces as $ns) {
                if(strpos($class, $ns) === 0) {
                    $this->dependencies[$ns][] = $class;
                }
            }
        }

        return $found;
    }
}
