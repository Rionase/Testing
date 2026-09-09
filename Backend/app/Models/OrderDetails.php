<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $id_order
 * @property int $id_product
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

    protected $table = 'order_detail';

    protected $fillable = [
        'id_order',
        'id_product',
        'name',
        'quantity',
        'price'
    ];
}
