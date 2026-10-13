<?php

namespace App\Filament\Resources\PrivacyPages;

use App\Filament\Resources\AboutPages\AboutPageResource;
use App\Filament\Resources\PrivacyPages\Pages\EditPrivacyPage;
use App\Filament\Resources\PrivacyPages\Pages\ListPrivacyPages;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class PrivacyPageResource extends AboutPageResource
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $navigationLabel = 'Privacy policy';

    protected static ?string $modelLabel = 'Privacy policy';

    protected static ?string $slug = 'privacy-policy';

    protected static ?int $navigationSort = 4;

    public static function pageSlug(): string
    {
        return 'privacy';
    }

    public static function hasStats(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrivacyPages::route('/'),
            'edit' => EditPrivacyPage::route('/{record}/edit'),
        ];
    }
}
