<?php
namespace Spot\Log\Impl;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;

class TwigMessageInterpolator {
    private $twig;
    
    public function __construct(
            /** @Named("log.twig.cache") */$cache = false) {
        $this->twig = new \Twig_Environment(new \Twig_Loader_String(), [
            'cache' => $cache
        ]);
        
        $this->twig->setLexer(new \Twig_Lexer($this->twig, [
            'tag_variable' => ['{', '}'],
        ]));
    }
    
    public function __invoke(array $record) {
        if (false === strpos($record['message'], '{')) {
            return $record;
        }

        $record['message'] = $this->twig->render($record['message'], $record['context']);
        
        return $record;
    }
    
    /** @Provides(Provides::ELEMENT) @Named("monolog.processors") @Singleton */
    static function provide(self $self) {
        return $self;
    }
}