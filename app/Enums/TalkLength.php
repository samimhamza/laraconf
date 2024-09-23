<?php

namespace App\Enums;

enum TalkLength: string
{
    case LIGHTENING = 'Lightening - 15 Minutes';
    case NORMAL = 'Normal - 30 Minutes';
    case KEYNOTE = 'Keynote';
}
