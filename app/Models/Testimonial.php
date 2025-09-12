<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $hidden = ['user_id'];

    protected $fillable = ['user_id', 'name', 'img', 'message', 'show'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
