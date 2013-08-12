<?php
namespace Spot\App\Cli\Deploy;

use Spot\App\Cli\Output;

interface DeploymentAgent {
    function deploy(Output $output);
    
    function getName();
}