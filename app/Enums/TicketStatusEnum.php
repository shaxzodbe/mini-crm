<?php

namespace App\Enums;

enum TicketStatusEnum: string
{
    case NEW = 'new';
    case IN_WORK = 'in-work';
    case PROCESSED = 'processed';
}
