<?php
namespace Spot\App\Web\Impl\Router\InvertedIndex;

use Spot\App\Web\Request;

class AjaxIndex {
    private $indexes;

    public function __construct(array $indexes) {
        $this->indexes = $indexes;
    }

    public function matches(Request $request) {
        return $this->indexes[$request->isAjax()];
    }
}
