<?php
namespace Spot\Domain;

use Spot\Domain\Impl\DomainImpl;
use Spot\Domain\Impl\PersistenceInterceptor;
use Spot\Domain\Impl\TransactionalInterceptor;
use Spot\Domain\Impl\ValidationInterceptor;
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

    /**
     * @Intercept(@AnnotatedWith(@Persist))
     * @Intercept(@AnnotatedWith(@Remove))
     */
    static public function providePersistenceInterceptor(PersistenceInterceptor $interceptor) {
        return $interceptor;
    }

    /** @Intercept(@AnnotatedWith(@Persist)) */
    static public function provideValidationInterceptor(ValidationInterceptor $interceptor) {
        return $interceptor;
    }

    /** @Intercept(@AnnotatedWith(@Transactional)) @Singleton */
    static public function provideTransactionalInterceptor(TransactionalInterceptor $interceptor) {
        return $interceptor;
    }
}
