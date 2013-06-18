<?php
namespace Spot\Module\Imagine;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Imagine\Gd\Imagine as GdImagine;
use Imagine\Imagick\Imagine as ImagickImagine;
use Imagine\Gmagick\Imagine as GmagickImagine;

class ImagineModule {
    /** @Provides("Imagine\Image\ImagineInterface") @Singleton */
    static function provideImagine(
            /** @Named("imagine.driver") */$driver = "gd") {
        switch(strtolower($driver)) {
            case "gd":
                return new GdImagine();
            case "imagick":
                return new ImagickImagine();
            case "gmagick":
                return new GmagickImagine();
        }
    }
}