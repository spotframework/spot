<?php
namespace Spot\Module\Assetic;

use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Extension\Twig\AsseticTokenParser;

class SpotAsseticExtension extends AsseticExtension {    
    public function getTokenParsers() {
        return [
            new AsseticTokenParser($this->factory, 'javascripts', '/dump/assets/js/*.js'),
            new AsseticTokenParser($this->factory, 'stylesheets', '/dump/assets/css/*.css'),
            new AsseticTokenParser($this->factory, 'image', '/dump/assets/images/*', true),
        ];
    }
}