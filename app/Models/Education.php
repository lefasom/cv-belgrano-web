<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    /** @use HasFactory<\Database\Factories\EducationFactory> */
    use HasFactory;

    protected $fillable = [
        'title_obtained',
        'institution',
        'period',
        'status',
        'sort_order',
    ];

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }


}
