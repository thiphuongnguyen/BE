<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $fillable = [
    	'order_id', 'color_id', 'product_id','product_image', 'product_name','product_price','product_sales_quantity'
    ];
    protected $primaryKey = 'order_detail_id';
 	protected $table = 'order_detail';

	protected $hidden = ['created_at', 'updated_at'];
 	// public function product(){
 	// 	return $this->belongsTo('App\Models\Product','product_id');
 	// }
}
