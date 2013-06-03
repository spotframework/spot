<?php
namespace Spot\App\Web\Impl\Binding;

use Spot\Code\CodeWriter;
use Spot\App\Web\Impl\Binding;

class ResponseBinding implements Binding {
    public function compile(CodeWriter $writer) {
        $writer->write('$rp');
    }    
}