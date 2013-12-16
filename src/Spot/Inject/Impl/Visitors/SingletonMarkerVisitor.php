<?php
namespace Spot\Inject\Impl\Visitors;

use Spot\Inject\Bindings\SingletonBinding;

class SingletonMarkerVisitor extends AbstractVisitor {
    private $count = 0;

    public function getCount() {
        return $this->count;
    }

    public function visitSingleton(SingletonBinding $singleton) {
        if($singleton->getIndex() !== null) {
            //already visited
            return;
        }

        $singleton->setIndex($this->count++);
    }
}
