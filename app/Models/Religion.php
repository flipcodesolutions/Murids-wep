<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Religion extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'status',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->image) {
                return asset('images/no-image.png');
            }

            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }

            $cleanPath = ltrim($this->image, '/');

            if (str_starts_with($cleanPath, 'images/')) {
                $cleanPath = substr($cleanPath, 7);
            }
            if (str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = substr($cleanPath, 8);
            }

            if (str_starts_with($cleanPath, 'religions/')) {
                return asset('images/' . $cleanPath);
            }

            return asset('images/religions/' . $cleanPath);
        });
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }
}
