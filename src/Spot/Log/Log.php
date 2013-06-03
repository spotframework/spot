<?php
namespace Spot\Log;

use Psr\Log\LogLevel;

/** @Annotation */
final class Log {
    public $value;
    
    public $level = LogLevel::DEBUG;
}