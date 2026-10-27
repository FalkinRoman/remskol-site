<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'phone', 'service', 'comment', 'photo_path', 'utm', 'page', 'ip', 'user_agent',
        'telegram_sent_at', 'telegram_error',
    ];

    protected function casts(): array
    {
        return [
            'utm' => 'array',
            'telegram_sent_at' => 'datetime',
        ];
    }
}
