<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;
    protected $fillable = ['color_name'];
    protected $primaryKey = 'color_id';
    protected $table = 'colors';

    protected $hidden = ['created_at', 'updated_at'];
}
