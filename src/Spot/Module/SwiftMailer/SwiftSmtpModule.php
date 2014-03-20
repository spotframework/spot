<?php
namespace Spot\Module\SwiftMailer;

use Spot\Inject\Provides;
use Spot\Inject\Named;

class SwiftSmtpModule {
    /** @Provides("Swift_Transport") */
    static public function provideSmtpTransport(
            /** @Named("swiftmailer.smtp.host") */$host,
            /** @Named("swiftmailer.smtp.port") */$port = 465,
            /** @Named("swiftmailer.smtp.username") */$username = null,
            /** @Named("swiftmailer.smtp.password") */$password = null,
            /** @Named("swiftmailer.smtp.security") */$security = "tls") {
        $smtp = \Swift_SmtpTransport::newInstance($host, $port, $security);
        $username && $smtp->setUsername($username);
        $password && $smtp->setPassword($password);

        return $smtp;
    }
}
