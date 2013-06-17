<?php
namespace Spot\Module\Assetic;

use Assetic\Factory\AssetFactory;

class SpotAssetFactory extends AssetFactory {
    private $root;
    
    public function __construct($root, $debug) {
        parent::__construct($root, $debug);
        
        $this->root = $root;
    }
    
    public function generateAssetName($inputs, $filters, $options = []) {
        $root = $this->root;
        
        return parent::generateAssetName(array_map(function ($input) use ($root) {    
            $content = "";
            foreach(glob("{$root}/{$input}") as $filename) {
                $content .= file_get_contents($filename);
            }
            
            return $content;
        }, $inputs), $filters, $options);
    }
}