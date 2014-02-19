<?php
namespace Spot\App\Web\Impl\Router\InvertedIndex;

use Spot\App\Web\Impl\Router\RoutePathCompiler;
use Spot\Gen\CodeWriter;
use Spot\Inject\Named;
use Spot\Reflect\Reflection;

class InvertedIndexGenerator {
    private $scanner,
            $compiler;

    public function __construct(
            ControllerScanner $scanner,
            RoutePathCompiler $compiler) {
        $this->scanner = $scanner;
        $this->compiler = $compiler;
    }

    public function generate(CodeWriter $writer) {
        $actions = $names = $methods = $staticPaths = $regexPaths = $prefixPaths = [];
        $ajax = [true => [], false => []];
        foreach($this->scanner->scan() as $i => $mapping) {
            $route = $mapping->route;
            $action = $mapping->action;

            if($this->compiler->checkSymbol($route->value)) {
                $pattern = $this->compiler->compile($route->value);

                $regexPaths[$i] = $pattern;

                $prefix = strtok($route->value, "*{(");
                $prefixPaths[$prefix][$i] = 1;
            } else {
                $staticPaths[$route->value][$i] = 1;
            }

            $actions[$i] = $action;
            foreach((array)$route->method as $method) {
                $methods[$method][$i] = 1;
            }

            if($route->ajax === null) {
                $ajax[true][$i] = $ajax[false][$i] = 1;
            } else {
                $ajax[$route->ajax][$i] = 1;
            }

            if($route->name) {
                $name = [
                    "params" => array_values($this->compiler->parse($route->value)),
                    "uri" => preg_replace('/\{\$(.*?)\}/', '$1', $route->value),
                ];

                $names[$route->name] = $name;
            }
        }

        $writer->write("function __construct() {");
        $writer->indent();
            $writer->write("parent::__construct(");
            $writer->indent();
                $writer->writeln(var_export($names, true), ", ");
                $writer->writeln(var_export($actions, true), ", ");
                $writer->writeln("new MethodIndex(", var_export($methods, true), "), ");
                $writer->writeln("new AjaxIndex(", var_export($ajax, true), "), ");
                $writer->writeln("new StaticPathIndex(", var_export($staticPaths, true), "), ");
                $writer->writeln("new PrefixPathIndex(", var_export($prefixPaths, true), "), ");
                $writer->write("new RegexPathIndex(", var_export($regexPaths, true), ")");
            $writer->outdent();
            $writer->write(");");
        $writer->outdent();
        $writer->writeln("}");
    }
}
