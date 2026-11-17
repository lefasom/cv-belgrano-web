<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    /** @use HasFactory<\Database\Factories\ExperienceFactory> */
    use HasFactory;

   protected $fillable = [
        'company_name',
        'position',
        'period',
        'description',
        'sort_order',
    ];
    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);         
    }
}
