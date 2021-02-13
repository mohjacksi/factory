<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Order extends Model
{
    use Filterable;

    /**
     * @var string
     */
    public $table = 'orders';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'user',
        'coupon',
        'OrderProducts'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'coupon_id',
        'details',
        'address',
        'phone_number',
        'subtotal',
        'total',
        'discount',
    ];

    /**
     * it defines foreign key in relations.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return 'order_id';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function OrderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
