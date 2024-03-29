<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','gallery_image'];
    protected $primaryKey = 'gallery_id';
    protected $table = 'galleries';

    protected $hidden = ['created_at', 'updated_at'];
}
