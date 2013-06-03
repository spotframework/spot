<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\Code\CodeStorage;
use Spot\Inject\Named;
use Spot\Inject\Lazy;

class IndexFactory {
    private $hash,
            $generator,
            $codeStorage;

    public function __construct(
            /** @Named("app.hash") */$hash,
            IndexGenerator $generator,
            CodeStorage $codeStorage) {
        $this->hash = $hash;
        $this->generator = $generator;
        $this->codeStorage = $codeStorage;
    }

    public function get() {
        $className = 'InvertedIndex__'.$this->hash;
        $fqcn = $this->codeStorage->load($className);
        if(empty($fqcn)) {
            $code = $this->generator->generate($className);

            $fqcn = $this->codeStorage->store($className, $code);
        }

        return new $fqcn;
    }
}