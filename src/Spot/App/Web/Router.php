<?php
namespace Spot\App\Web;

interface Router {
    function resolve(Request $request);

    function generate($name, array $params = []);
}
