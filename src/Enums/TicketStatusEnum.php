<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketStatusEnum: string
{
    case UnPaid = 'unpaid';
    case Paid = 'paid';
}
