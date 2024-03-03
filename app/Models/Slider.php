<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['slider_name','slider_image','slider_status'];
    protected $primaryKey = 'slider_id';
    protected $table = 'sliders';

    protected $hidden = ['created_at', 'updated_at'];
}
