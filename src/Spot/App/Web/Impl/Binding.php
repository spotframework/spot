<?php
namespace Spot\App\Web\Impl;

use Spot\Code\CodeWriter;

interface Binding {
    function compile(CodeWriter $writer);
}