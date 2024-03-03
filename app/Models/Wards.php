<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wards extends Model
{
    use HasFactory;

    protected $table = 'wards';
    protected $primaryKey = 'wards_id';
    public $timestamps = false;

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
        // return $this->belongsTo('App\Models\District', 'district_id');
    }
}
