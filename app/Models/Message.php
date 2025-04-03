<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['telegram_user_id', 'sender', 'message'];

    public function telegramUser()
    {
        return $this->belongsTo(TelegramUser::class);
    }
}

