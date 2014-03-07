<?php
namespace Spot\Module\SwiftMailer;

use Spot\Inject\Provides;
use Spot\Inject\Named;

class SwiftSmtpModule {
    /** @Provides("Swift_Transport") */
    static public function provideSmtpTransport(
            /** @Named("swiftmailer.smtp.host") */$host,
            /** @Named("swiftmailer.smtp.port") */$port,
            /** @Named("swiftmailer.smtp.security") */$security = "tls") {
        return \Swift_SmtpTransport::newInstance($host, $port, $security);
    }
}
