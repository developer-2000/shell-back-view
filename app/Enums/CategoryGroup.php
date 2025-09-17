<?php

declare(strict_types=1);

namespace App\Enums;

enum CategoryGroup: string {
    case RBA = 'RBA';
    case DO_PLUS = 'DO+';
    case DO = 'DO';
    case DO_EXP = 'DO/EXP';
    case AUT_CRT = 'AUT/CRT';
    case DO_COM = 'DO-COM';
    case RBA_CRT = 'RBA/CRT';
    case DO_CRT = 'DO/CRT';
    case AUT_EXP = 'AUT/EXP';
}
