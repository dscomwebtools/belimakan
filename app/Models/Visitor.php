<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'count', 'ip_address', 'user_agent', 'is_bot', 'last_increment_at'];
}
