<?php
namespace Spot\Inject\Impl;

use Spot\Gen\CodeStorage;
use Spot\Gen\CodeWriter;
use Spot\Inject\Key;
use Spot\Reflect\Reflection;

class LazyFactory {
    private $reflection,
            $codeStorage,
            $lazyGen;

    public function __construct(
            Reflection $reflection,
            CodeStorage $codeStorage,
            LazyGenerator $lazyGen) {
        $this->reflection = $reflection;
        $this->codeStorage = $codeStorage;
        $this->lazyGen = $lazyGen;
    }

    public function get(Key $key) {
        $lazyClass = "Lazy__".$key->hash();
        if($this->codeStorage->load($lazyClass)) {
            return $lazyClass;
        }

        $type = $this->reflection->get($key->getType());
        $writer = new CodeWriter();

        $writer->write("class ", $lazyClass);
        $type->isInterface()
            ? $writer->write(" implements ")
            : $writer->write(" extends ");
        $writer->write($type->name, " {");
        $this->lazyGen->generate($type, $writer);
        $writer->write("}");

        $this->codeStorage->store($lazyClass, $writer);

        return $lazyClass;
    }
}
