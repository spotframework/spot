<?php
namespace Spot\Domain\Impl;

use Spot\Gen\CodeStorage;
use Spot\Gen\CodeWriter;

class BinderFactory {
    private $storage,
            $gen;

    public function __construct(
            CodeStorage $storage,
            BinderGenerator $gen) {
        $this->storage = $storage;
        $this->gen = $gen;
    }

    public function getBinder($domainName) {
        $binder = "DomainBinder__".md5($domainName);
        if($this->storage->load($binder)) {
            return $binder;
        }

        $writer = new CodeWriter();
        $writer->write("class ", $binder, "{");
        $writer->indent();
            $writer->write('static function newInstance($d, $n, $b) {');
            $writer->indent();
                $this->gen->generateNewInstance($domainName, $writer);
            $writer->outdent();
            $writer->writeln("}");

            $writer->write('static function bind($d, $i, $b) {');
            $writer->indent();
                $this->gen->generateBind($domainName, $writer);
            $writer->outdent();
            $writer->writeln("}");
        $writer->outdent();
        $writer->write("}");

        $this->storage->store($binder, $writer);

        return $binder;
    }
}
