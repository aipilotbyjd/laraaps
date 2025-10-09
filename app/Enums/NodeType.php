<?php

namespace App\Enums;

enum NodeType: string
{
    case TRIGGER = 'trigger';
    case ACTION = 'action';
    case LOGIC = 'logic';
}
