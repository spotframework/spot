<?php
namespace Spot\Module\SwiftMailer;

use Spot\Inject\Provides;
use Spot\Inject\Singleton;

class SwiftMailerModule {
    /** @Provides("Swift_Mailer") @Singleton */
    static public function provideSwiftMailer(\Swift_Transport $transport) {
        $swiftmailer = \Swift_Mailer::newInstance($transport);

        return $swiftmailer;
    }
}
