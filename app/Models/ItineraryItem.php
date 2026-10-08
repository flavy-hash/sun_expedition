<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'day_number',
        'title',
        'description',
        'image',
        'image_caption',
        'note',
        'activity',
        'accommodation',
    ];

    protected $casts = [
        'activity' => 'array',
        'accommodation' => 'array',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
