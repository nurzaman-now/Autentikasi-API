<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory, Timestamp;

    protected $primaryKey = 'email';
    protected $fillable = [
        'email',
        'token',
        'reset_token',
        'type',
    ];

    protected $casts = [
        'email' => 'string',
        'token' => 'string',
        'reset_token' => 'string',
        'type' => 'string'
    ];
}
