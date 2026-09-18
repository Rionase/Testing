<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case INITIATED = 'INITIATED';
    case PENDING = 'PENDING';
    case AUTHORIZE = 'AUTHORIZE';
    case CAPTURE = 'CAPTURE';
    case SETTLEMENT = 'SETTLEMENT';
    case DENY = 'DENY';
    case CANCEL = 'CANCEL';
    case EXPIRE = 'EXPIRE';
    case FAILURE = 'FAILURE';
    case REFUND = 'REFUND';
    case PARTIAL_REFUND = 'PARTIAL_REFUND';
    case CHARGEBACK = 'CHARGEBACK';
    case PARTIAL_CHARGEBACK = 'PARTIAL_CHARGEBACK';
}
