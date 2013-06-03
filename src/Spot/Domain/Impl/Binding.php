<?php
namespace Spot\Domain\Impl;

use Spot\Code\CodeWriter;

interface Binding {
    function compile(CodeWriter $writer);
}