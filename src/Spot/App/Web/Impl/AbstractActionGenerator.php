<?php
namespace Spot\App\Web\Impl;

use Spot\Reflect\Method;
use Spot\Code\CodeWriter;
use Spot\Code\Impl\CodeWriterImpl;

abstract class AbstractActionGenerator {    
    public function generate($className, Method $method) {
        $writer = new CodeWriterImpl();
        
        $writer->writeln('use Spot\Http\Request;');
        $writer->writeln('use Spot\Http\Response;');
        $writer->writeln('use Spot\App\Web\Impl\Action;');
        $writer->writeln('use Spot\Domain\DomainManager;');
        $writer->writeln('use Spot\Inject\Injector;');
        $writer->newLine();
        
        $writer->writeln('/**');
        $writer->write(' * Generated action adapter of ');
        $writer->write($method->getType()->name);
        $writer->write('::');
        $writer->writeln($method->name);
        $writer->writeln(' */');
        
        $writer->write('class ');
        $writer->write($className);
        $writer->write(' implements Action {');
        $writer->indent();
            $writer->writeln('public $d, $i;');
            $writer->newLine();
            
            $writer->write('function __construct(DomainManager $domain, Injector $injector) {');
            $writer->indent();
                $writer->writeln('$this->d = $domain;');
                $writer->write('$this->i = $injector;');
            $writer->outdent();
            $writer->writeln('}');
            $writer->newLine();
        
            $writer->write('function invoke(Request $rq, Response $rp) {');
            $writer->indent();

                $this->generateBody($method, $writer);
            
            $writer->outdent();
            $writer->write('}');
        $writer->outdent();
        $writer->write('}');
            
        return $writer->getCode();
    }
    
    public abstract function generateBody(Method $method, CodeWriter $writer);
}