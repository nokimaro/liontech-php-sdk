<?php

declare(strict_types=1);

namespace LionTech\SDK\Enums;

enum PaymentMethodType: string
{
    case CARD = 'card';
    case SBP = 'sbp';
}
