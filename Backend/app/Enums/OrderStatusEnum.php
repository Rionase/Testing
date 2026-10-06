<?php

namespace App\Enums;

use App\Exceptions\ValidationException;

enum OrderStatusEnum: int
{
    // id selalu perlu dicocokan dengan database tabel order_status
    case INITIATED = 1;
    case PENDING = 2;
    case CAPTURE = 3;
    case SETTLEMENT = 4;
    case DENY = 5;
    case CANCEL = 6;
    case EXPIRE = 7;
    case FAILURE = 8;
    case REFUND = 9;
    case CHARGEBACK = 10;
    case PARTIAL_REFUND = 11;
    case PARTIAL_CHARGEBACK = 12;
    case AUTHORIZE = 13;
    case ON_DELIVERY = 14;
    case FINISHED = 15;


    public function id(): int
    {
        return $this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::INITIATED => 'INITIATED',
            self::PENDING => 'PENDING',
            self::CAPTURE => 'CAPTURE',
            self::SETTLEMENT => 'SETTLEMENT',
            self::DENY => 'DENY',
            self::CANCEL => 'CANCEL',
            self::EXPIRE => 'EXPIRE',
            self::FAILURE => 'FAILURE',
            self::REFUND => 'REFUND',
            self::CHARGEBACK => 'CHARGEBACK',
            self::PARTIAL_REFUND => 'PARTIAL_REFUND',
            self::PARTIAL_CHARGEBACK => 'PARTIAL_CHARGEBACK',
            self::AUTHORIZE => 'AUTHORIZE',
            self::ON_DELIVERY => 'ON_DELIVERY',
            self::FINISHED => 'FINISHED',
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
            'CAPTURE'            => self::CAPTURE,
            'SETTLEMENT'         => self::SETTLEMENT,
            'DENY'               => self::DENY,
            'CANCEL'             => self::CANCEL,
            'EXPIRE'             => self::EXPIRE,
            'FAILURE'            => self::FAILURE,
            'REFUND'             => self::REFUND,
            'CHARGEBACK'         => self::CHARGEBACK,
            'PARTIAL_REFUND'     => self::PARTIAL_REFUND,
            'PARTIAL_CHARGEBACK' => self::PARTIAL_CHARGEBACK,
            'AUTHORIZE'          => self::AUTHORIZE,
            'ON_DELIVERY'        => self::ON_DELIVERY,
            'FINISHED'           => self::FINISHED,
        ];

        if (!isset($map[$label])) {
            throw new ValidationException('Order Status not found.');
        }

        return $map[$label]->id();
    }

}
