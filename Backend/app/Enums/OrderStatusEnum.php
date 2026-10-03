<?php

namespace App\Enums;

use App\Exceptions\ValidationException;

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
            self::INITIATED => 'INITIATED',
            self::PENDING => 'PENDING',
            self::AUTHORIZE => 'AUTHORIZE',
            self::CAPTURE => 'CAPTURE',
            self::SETTLEMENT => 'SETTLEMENT',
            self::DENY => 'DENY',
            self::CANCEL => 'CANCEL',
            self::EXPIRE => 'EXPIRE',
            self::FAILURE => 'FAILED',
            self::REFUND => 'REFUND',
            self::PARTIAL_REFUND => 'PARTIAL_REFUND',
            self::CHARGEBACK => 'CHARGEBACK',
            self::PARTIAL_CHARGEBACK => 'PARTIAL_CHARGEBACK',
        };
    }

    /**
     * @throws ValidationException
     */
    public static function idFromLabel(string $label): ?int
    {
        $label = strtoupper($label);

        $map = [
            'INITIATED'          => self::INITIATED,
            'PENDING'            => self::PENDING,
            'AUTHORIZE'          => self::AUTHORIZE,
            'CAPTURE'            => self::CAPTURE,
            'SETTLEMENT'         => self::SETTLEMENT,
            'DENY'               => self::DENY,
            'CANCEL'             => self::CANCEL,
            'EXPIRE'             => self::EXPIRE,
            'FAILURE'            => self::FAILURE,
            'REFUND'             => self::REFUND,
            'PARTIAL_REFUND'     => self::PARTIAL_REFUND,
            'CHARGEBACK'         => self::CHARGEBACK,
            'PARTIAL_CHARGEBACK' => self::PARTIAL_CHARGEBACK,
        ];

        if (!isset($map[$label])) {
            throw new ValidationException('Order Status not found.');
        }

        return $map[$label]->id();
    }

}
