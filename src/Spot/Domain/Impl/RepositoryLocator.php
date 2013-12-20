<?php
namespace Spot\Domain\Impl;

use Spot\Domain\Repository;
use Spot\Inject\Injector;
use Spot\Reflect\Match;
use Spot\Reflect\Reflection;
use Doctrine\Common\Cache\Cache;

class RepositoryLocator {
    private $cache,
        $injector,
        $reflection;

    public function __construct(
            Cache $cache,
            Injector $injector,
            Reflection $reflection) {
        $this->cache = $cache;
        $this->injector = $injector;
        $this->reflection = $reflection;
    }

    public function findRepositoryName($className) {
        //Check repository name using convention of "Repository" suffix
        //in the same namespace.
        //C.o.C is awesome, you should use it !!
        $repoName = "{$className}Repository";
        if( class_exists($repoName)
            &&
            in_array("Spot\\Domain\\Repository", class_implements($repoName))
            &&
            $repoName::repositoryOf() == $className) {
            return $repoName;
        }

        $repos = $this->reflection->find(
            substr($className, 0, strpos($className, "\\")), //get class NS
            Match::instantiableOnly()->andIt(
                Match::subTypeOf("Spot\\Domain\\Repository"))
        );

        foreach($repos as $repo) {
            $repoName = $repo->name;
            if($repoName::repositoryOf() == $className) {
                return $repoName;
            }
        }

        // search class inheritance hierarchies
        $candidates = [];
        $classParents = array_values(class_parents($className) ?: []);
        foreach($repos as $repo) {
            $repoName = $repo->name;
            $index = array_search($repoName::repositoryOf(), $classParents);
            if($index !== false) {
                $candidates[$index] = $repoName;
            }
        }

        if(!empty($candidates)) {
            // check closest candidates
            return $candidates[min(array_keys($candidates))];
        }

        throw new \RuntimeException("Repository of {$className} can't be found");
    }

    /**
     * @param string $className
     * @return Repository
     */
    public function find($className) {
        $id = "repo#{$className}";
        $repoName = $this->cache->fetch($id);
        if(!$repoName) {
            $repoName = $this->findRepositoryName($className);

            $this->cache->save($id, $repoName);
        }

        return $this->injector->getInstance($repoName);
    }
}
