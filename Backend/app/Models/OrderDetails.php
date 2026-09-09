<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $id_orders
 * @property int $id_products
 * @property string $name
 * @property int $quantity
 * @property int $price
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
        'id_products',
        'name',
        'quantity',
        'price'
    ];
}
