<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $table = 'shipping';
    protected $primaryKey = 'shipping_id';
    public $timestamps = true;

    protected $fillable = ['shipping_name','shipping_address','shipping_phone','shipping_notes'];

    protected $hidden = ['created_at', 'updated_at'];
    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_id', 'shipping_id');
    }
}
