<?php
namespace Spot\Domain;

use Spot\Domain\Impl\Command\FindCommand;
use Spot\Domain\Impl\DomainImpl;
use Spot\Domain\Impl\TransactionalInterceptor;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Inject\Intercept;
use Spot\Inject\Aspect\Matcher\AnnotatedWith;

class DomainModule {
    /** @Provides("Spot\Domain\Domain") @Singleton */
    static public function provideDomain(DomainImpl $domain) {
        return $domain;
    }

    /** @Intercept(@AnnotatedWith(@Transactional)) */
    static public function provideTransactionalInterceptor(TransactionalInterceptor $interceptor) {
        return $interceptor;
    }
}
