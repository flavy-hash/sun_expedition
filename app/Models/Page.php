<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'title_highlight',
        'intro',
        'body',
        'stats',
    ];

    protected $casts = [
        'stats' => 'array',
    ];

    public function paragraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split("/\R\s*\R/", (string) $this->body))));
    }
}
