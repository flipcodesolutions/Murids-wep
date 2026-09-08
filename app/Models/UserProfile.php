<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'religion_id',
        'notification_enabled',
        'device_token',
        'timezone',
        'date_of_birth',
        'gender'
    ];

    protected function casts(): array
    {
        return [
            'notification_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }
}
