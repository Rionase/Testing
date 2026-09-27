<?php

namespace App\Enums;

enum OrderStatusEnum: int
{
    // id selalu perlu dicocokan dengan database tabel order_status
    case INITIATED = 1;
    case PENDING = 2;
    case AUTHORIZE = 3;
    case CAPTURE = 4;
    case SETTLEMENT = 5;
    case DENY = 6;
    case CANCEL = 7;
    case EXPIRE = 8;
    case FAILURE = 9;
    case REFUND = 10;
    case PARTIAL_REFUND = 11;
    case CHARGEBACK = 12;
    case PARTIAL_CHARGEBACK = 13;

    public function id(): int
    {
        return $this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::INITIATED => 'Initiated',
            self::PENDING => 'Pending Payment',
            self::AUTHORIZE => 'Authorized',
            self::CAPTURE => 'Captured',
            self::SETTLEMENT => 'Settlement Completed',
            self::DENY => 'Denied',
            self::CANCEL => 'Canceled',
            self::EXPIRE => 'Expired',
            self::FAILURE => 'Failed',
            self::REFUND => 'Fully Refunded',
            self::PARTIAL_REFUND => 'Partially Refunded',
            self::CHARGEBACK => 'Chargeback',
            self::PARTIAL_CHARGEBACK => 'Partially Chargebacked',
        };
    }

}
