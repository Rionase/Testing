<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $id_orders
 * @property string $nama
 * @property int $quantity
 * @property int $harga_total
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class OrderDetails extends Model
{
    use SoftDeletes;

    protected $table = 'order_details';

    protected $fillable = [
        'id_orders',
        'nama',
        'quantity',
        'harga_total',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
