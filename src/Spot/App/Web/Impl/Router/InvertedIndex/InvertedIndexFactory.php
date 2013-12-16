<?php
namespace Spot\App\Web\Impl\Router\InvertedIndex;

use Spot\Gen\CodeStorage;
use Spot\Gen\CodeWriter;
use Spot\Inject\Named;

class InvertedIndexFactory {
    private $hash,
            $gen,
            $storage;

    public function __construct(
            /** @Named("app.hash") */$hash,
            InvertedIndexGenerator $gen,
            CodeStorage $storage) {
        $this->hash = $hash;
        $this->gen = $gen;
        $this->storage = $storage;
    }

    public function get() {
        $router = "InvertedIndex__{$this->hash}";
        if($this->storage->load($router)) {
            return new $router();
        }

        $writer = new CodeWriter();
        $writer->writeln("use Spot\\App\\Web\\Impl\\Router\\InvertedIndexRouter;");
        $writer->writeln("use Spot\\App\\Web\\Impl\\Router\\InvertedIndex\\MethodIndex;");
        $writer->writeln("use Spot\\App\\Web\\Impl\\Router\\InvertedIndex\\AjaxIndex;");
        $writer->writeln("use Spot\\App\\Web\\Impl\\Router\\InvertedIndex\\StaticPathIndex;");
        $writer->writeln("use Spot\\App\\Web\\Impl\\Router\\InvertedIndex\\PrefixPathIndex;");
        $writer->writeln("use Spot\\App\\Web\\Impl\\Router\\InvertedIndex\\RegexPathIndex;");
        $writer->write("class ", $router, " extends InvertedIndexRouter {");
        $writer->indent();
            $this->gen->generate($writer);
        $writer->outdent();
        $writer->writeln("}");

        $this->storage->store($router, $writer);

        return new $router();
    }
}
