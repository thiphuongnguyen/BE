<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ['coupon_code','coupon_discount','coupon_expiry_date'];
    protected $primaryKey = 'coupon_id';
    protected $table = 'coupon';

    protected $hidden = ['created_at', 'updated_at'];
}
