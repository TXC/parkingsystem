<?php

declare(strict_types=1);

namespace App\Enums;

enum InfractionEnum: string
{
    case Overdue = 'overdue';
    case NoPayment = 'nopayment';
}
