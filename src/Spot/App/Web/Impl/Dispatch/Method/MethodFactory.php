<?php
namespace Spot\App\Web\Impl\Dispatch\Method;

use Spot\Gen\CodeStorage;
use Spot\Gen\CodeWriter;
use Spot\Reflect\Reflection;

class MethodFactory {
    private $storage,
            $reflection,
            $static,
            $instance;

    public function __construct(
            CodeStorage $storage,
            Reflection $reflection,
            StaticMethodGenerator $static,
            InstanceMethodGenerator $instance) {
        $this->storage = $storage;
        $this->reflection = $reflection;
        $this->static = $static;
        $this->instance = $instance;
    }

    public function get(array $action) {
        $adapter = "ActionAdapter__".md5("{$action[0]}::{$action[1]}");
        if($this->storage->load($adapter)) {
            return $adapter;
        }

        $method = $this->reflection->get($action[0])->getMethod($action[1]);
        $writer = new CodeWriter();
        $writer->write("class ", $adapter, " {");
        $writer->indent();
            $method->isStatic()
                ? $this->static->generate($method, $writer)
                : $this->instance->generate($method, $writer);
        $writer->outdent();
        $writer->writeln("}");

        $this->storage->store($adapter, $writer);

        return $adapter;
    }
}
