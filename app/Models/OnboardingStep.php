<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingStep extends Model
{
    protected $table = 'onboarding_step';

    protected $fillable = [
        'religion_id',
        'question',
        'yes_response',
        'no_response',
        'yes_response_title',
        'no_response_title'
    ];

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }
}
