<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $fillable = [
    	'admin_name', 'admin_password', 'admin_phone','admin_image','admin_role'
    ];
    protected $primaryKey = 'admin_id';
 	protected $table = 'admin';
    protected $hidden = ['created_at', 'updated_at'];
}
