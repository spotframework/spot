<?php
namespace Spot\Module\Spot;

use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Inject\Intercept;
use Spot\Inject\Matcher\AnnotatedWith;
use Spot\Inject\Matcher\HasParameterAnnotatedWith;
use Spot\Domain\Remove;
use Spot\Domain\Persist;
use Spot\Domain\Validate;
use Spot\Domain\Transactional;
use Spot\Domain\Impl\ManagerImpl;
use Spot\Domain\Impl\TransactionalInterceptor;
use Spot\Domain\Impl\PersistenceInterceptor;
use Spot\Domain\Impl\ValidationInterceptor;

class DomainModule {
    /** @Provides("Spot\Domain\DomainManager") @Singleton */
    static function provideManager(ManagerImpl $impl) {
        return $impl;
    }
    
    /**
     * @Intercept(@AnnotatedWith(@Transactional))
     *  
     * @Provides("Spot\Domain\Impl\TransactionalInterceptor") @Singleton
     */
    static function provideTransactionalInterceptor(TransactionalInterceptor $i) {
        return $i;
    }
    
    /**
     * @Intercept(@HasParameterAnnotatedWith(@Remove))
     * @Intercept(@HasParameterAnnotatedWith(@Persist))
     * 
     * @Provides("Spot\Domain\Impl\PersistenceInterceptor") @Singleton
     */
    static function providePersistenceInterceptor(PersistenceInterceptor $i) {
        return $i;
    }
    
    /**
     * @Intercept(@HasParameterAnnotatedWith("Spot\Domain\Validate"))
     * 
     * @Provides("Spot\Domain\Impl\ValidationInterceptor") @Singleton
     */
    static function provideValidationInterceptor(ValidationInterceptor $i) {
        return $i;
    }
}