<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int $id
 * @property int $gross_ammount
 * @property string $keterangan
 * @property string $status
 * @property string $snap_token
 * @property string $snap_redirect_url
 * @property Carbon $payment_time
 * @property Carbon $expired_at
 * @property Carbon $snap_created_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class Order extends Model
{
    use SoftDeletes;

    protected $table = 'order';

    protected $fillable = [
        'gross_ammount',
        'keterangan',
        'status',
        'snap_token',
        'snap_redirect_url',
        'payment_time',
        'expired_at',
        'snap_created_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
