<?php

namespace App\Enums;

use App\Traits\EnumOperation;

enum TrilioStatus: string
{
    use EnumOperation;
    case PENDING = 'PENDING';
    case INPROGRESS = 'INPROGRESS';
    case COMPLETED = 'COMPLETED';
}
