<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    /** @use HasFactory<\Database\Factories\SkillFactory> */
    use HasFactory;


    protected $fillable = [
        'name',
        'sort_order',
    ];  

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
