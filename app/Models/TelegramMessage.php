<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TelegramMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_user_id',
        'content',
        'from_admin',
        'is_read',
    ];

    protected $casts = [
        'from_admin' => 'boolean',
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id');
    }
}
