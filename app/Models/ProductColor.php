<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','color_id', 'quantity'];
    protected $primaryKey = 'product_color_id';
    protected $table = 'product_color';

    protected $hidden = ['created_at', 'updated_at'];
}
