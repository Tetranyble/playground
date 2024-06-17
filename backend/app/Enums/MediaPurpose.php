<?php

namespace App\Enums;

enum MediaPurpose: string
{
    case BANNER = 'BANNER';
    case GENERAL = 'GENERAL';

    case IMAGE = 'IMAGE';
    case PROFILE = 'PROFILE';

    case LOGO = 'LOGO';
    case FAVICON = 'FAVICON';

}
