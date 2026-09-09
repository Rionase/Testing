<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int $id
 * @property int $id_order_status
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property string $notes
 * @property int $total_price
 *
 * @property string $snap_token
 * @property string $snap_redirect_url
 * @property Carbon $expired_at
 * @property Carbon $payment_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class Order extends Model
{
    use SoftDeletes;

    protected $table = 'order';

    protected $fillable = [
        'id_order_status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'total_price',
        'snap_token',
        'snap_redirect_url',
        'expired_at',
        'payment_time',
    ];
}
