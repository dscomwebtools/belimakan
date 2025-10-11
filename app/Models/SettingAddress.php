<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingAddress extends Model
{
    use HasFactory;

    protected $table = 'setting_address';
    protected $fillable = [
        'name', 'hp', 'email', 'address', 'area_id',
        'area_name', 'postal_code', 'latitude', 'longitude', 'province', 'city', 'district',
    ];
}
