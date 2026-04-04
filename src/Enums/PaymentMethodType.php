<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Enums;

enum PaymentMethodType: string
{
    case CARD = 'card';
    case SBP = 'sbp';
}
