<?php

namespace App\Enums;

use App\Traits\EnumOperation;

enum Disk: string
{
    use EnumOperation;

    case PRIVATE = 'local';
    case PUBLIC = 'public';
    case S3PRIVATE = 's3-private';
    case S3PUBLIC = 's3-public';
    case CLOUDINARY = 'cloudinary';
    case GOOGLE = 'google';
    case YOUTUBE = 'youtube';
    case VIMEO = 'video';

    case FTP = 'ftp';
}
