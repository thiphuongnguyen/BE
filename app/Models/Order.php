<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // public $timestamps = false;
    protected $fillable = ['customer_id','shipping_id','payment_id','order_total','order_status'];
    protected $primaryKey = 'order_id';
    protected $table = 'order';

    public function shipping()
    {
        return $this->belongsTo(Shipping::class, 'shipping_id', 'shipping_id');
    }
    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }
}
