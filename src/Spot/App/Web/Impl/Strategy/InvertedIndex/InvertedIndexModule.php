<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\Inject\Provides;

trait InvertedIndexModule {
    /** @Provides("Spot\App\Web\Impl\RoutingStrategy") */
    static function provideStrategy(IndexFactory $factory) {
        return $factory->get();
    }
}