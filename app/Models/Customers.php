<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Customers extends Model
{
    use HasFactory;
    use HasApiTokens;

    // public $timestamps = false;
    protected $fillable = ['customer_name','customer_password','customer_phone','customer_token'];
    protected $primaryKey = 'customer_id';
    protected $table = 'customers';

    // protected $hidden = ['created_at', 'updated_at'];

    public function cart()
    {
        return $this->hasMany(Cart::class, 'customer_id', 'customer_id');
    }
}
