<?php
namespace Spot\Domain\Impl;

use Spot\Reflect\Match;
use Spot\Inject\Injector;
use Spot\Reflect\Reflection;

class RepositoryFinder {
    private $injector,
            $reflection,
            $repositories = [];
    
    public function __construct(
            Injector $injector, 
            Reflection $reflection) {
        $this->injector = $injector;
        $this->reflection = $reflection;
    }

    public function getRepositoryOf($className) {
        if(isset($this->repositories[$className])) {
            return $this->repositories[$className];
        }
        
        // Check class name using convention of "Repository" suffix 
        // in the same namespace.
        // Convention over Configuration is awesome, you should use it !!
        if( class_exists($repoName = $className."Repository") 
            &&
            in_array("Spot\Domain\Repository", class_implements($repoName))
            &&
            $repoName::repositoryOf() === $className) {
            return $this->repositories[$className] = $repoName;
        }
        
        $repos = $this->reflection->find(
            substr($className, 0, strpos($className, "\\")),
            Match::instantiableOnly()->andIt(Match::subTypeOf("Spot\Domain\Repository"))
        );
        
        foreach($repos as $repo) {
            $repoName = $repo->name;
            if($repoName::repositoryOf() === $className) {
                return $this->repositories[$className] = $repoName;
            }
        }
        
        // search class inheritance hierarchies
        $candidates = [];
        $classParents = array_values(class_parents($className));
        foreach($repos as $repo) {
            $repoName = $repo->name;
            if(($index = array_search($repoName::repositoryOf(), $classParents)) !== false) {
                $candidates[$index] = $repoName;
            }
        }
        
        if(!empty($candidates)) {
            // check closest candidates
            $repoName = $candidates[min(array_keys($candidates))];
            
            return $this->repositories[$className] = $repoName;
        }
        
        throw new \LogicException("Repository of {$className} can't be found, where the hell did you hide it ?");
    }
    
    public function get($className) {
        return $this->injector->getInstance($this->getRepositoryOf($className));
    }
}