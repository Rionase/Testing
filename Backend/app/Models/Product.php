<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $price
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class Product extends Model
{
    use SoftDeletes;

    protected $table = 'product';

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
    ];
}
