<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id'
    ];

    protected $casts = [
        'safe_mode' => 'boolean'
    ];
}
