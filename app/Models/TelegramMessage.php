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
        'file_path',
        'file_type',
        'file_name',
    ];

    protected $casts = [
        'from_admin' => 'boolean',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the message.
     */
    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id');
    }

    /**
     * Alias for telegramUser() for backward compatibility
     */
    public function user(): BelongsTo
    {
        return $this->telegramUser();
    }
}
