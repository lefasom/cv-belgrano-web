<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cv extends Model
{
    /** @use HasFactory<\Database\Factories\CvFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'full_name',
        'headline',
        'email',
        'phone',
        'location',
        'summary',
        'template',
    ];

        public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
