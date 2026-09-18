<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int $id
 * @property string $name
 * @property boolean $is_midtrans_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class OrderStatus extends Model
{
    use SoftDeletes;

    protected $table = 'order_status';

    protected $fillable = [
        'name',
        'is_midtrans_status'
    ];
}
