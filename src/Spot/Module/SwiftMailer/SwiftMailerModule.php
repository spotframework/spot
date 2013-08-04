<?php
namespace Spot\Module\SwiftMailer;

use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Inject\Named;

class SwiftMailerModule {    
    /** @Provides("Swift_Transport") @Singleton */
    static function provideSwiftTransport(
            /** @Named("swiftmailer.host") */$host = "localhost",
            /** @Named("swiftmailer.port") */$port = 25,
            /** @Named("swiftmailer.username") */$username = null,
            /** @Named("swiftmailer.password") */$password = null,
            /** @Named("swiftmailer.security") */$security = null) {
        $transport = \Swift_SmtpTransport::newInstance($host, $port, $security);
        $username && $transport->setUsername($username);
        $password && $transport->setPassword($password);
        
        return $transport;
    }
    
    /** @Provides("Swift_Mailer") @Singleton */
    static function provideSwiftMailer(
            \Swift_Transport $transport,
            /** @Named("swiftmailer.plugins") */array $plugins = []) {
        $mailer = \Swift_Mailer::newInstance($transport);
        foreach($plugins as $plugin) {
            $mailer->registerPlugin($plugin);
        }
        
        return $mailer;
    }
}
