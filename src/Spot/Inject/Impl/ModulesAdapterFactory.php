<?php
namespace Spot\Inject\Impl;

use Spot\Gen\CodeStorage;
use Spot\Gen\CodeWriter;

class ModulesAdapterFactory {
    private $builder,
            $singletons,
            $storage;

    public function __construct(
            BindingsBuilder $builder,
            Singletons $singletons,
            CodeStorage $storage) {
        $this->builder = $builder;
        $this->singletons = $singletons;
        $this->storage = $storage;
    }

    public function get(Modules $modules) {
        $adapter = "ModulesAdapter__".$modules->hash();
        if($this->storage->load($adapter)) {
            return $adapter;
        }

        $this->builder->build();

        $writer = new CodeWriter();
        $writer->write("class ", $adapter, " {");
        $writer->indent();
            $writer->write("const SINGLETONS_SIZE = ", $this->singletons->getSize(), ";");
        $writer->outdent();
        $writer->write("}");

        $this->storage->store($adapter, $writer);

        return $adapter;
    }
}
