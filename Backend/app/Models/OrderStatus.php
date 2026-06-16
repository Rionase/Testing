<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $gross_ammount
 * @property string $keterangan
 * @property int $id_order_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class OrderStatus extends Model
{
    protected $table = 'order';

    protected $fillable = [
        'nama',
        'created_at',
        'updated_at',
    ];
}
