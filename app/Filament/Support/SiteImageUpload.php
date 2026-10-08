<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

class SiteImageUpload
{
    // Resized in the browser before upload so full-size camera photos don't slow down the public site.
    public static function make(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk('site')
            ->directory('uploads/' . $directory)
            ->visibility('public')
            ->maxSize(10240)
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('2000')
            ->imageResizeTargetHeight('2000')
            ->imageResizeUpscale(false);
    }
}
