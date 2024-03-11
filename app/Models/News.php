<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $fillable = ['news_name', 'news_content','news_image','news_status'];
    protected $primaryKey = 'news_id';
    protected $table = 'news';

    // protected $hidden = ['created_at', 'updated_at'];
}
