<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int $id
 * @property int $gross_ammount
 * @property string $keterangan
 * @property string $midtrans_payment_status
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
        'midtrans_payment_status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
