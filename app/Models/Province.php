<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $table = 'province';
    protected $primaryKey = 'province_id';
    public $timestamps = false;

    public function districts()
    {
        return $this->hasMany(District::class, 'province_id', 'province_id');
    }
}
