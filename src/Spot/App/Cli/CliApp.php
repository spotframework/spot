<?php
namespace Spot\App\Cli;

interface CliApp {
    function handle(Argv $args);
}