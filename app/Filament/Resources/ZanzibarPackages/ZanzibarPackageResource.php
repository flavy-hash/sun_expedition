<?php

namespace App\Filament\Resources\ZanzibarPackages;

use App\Filament\Resources\ZanzibarPackages\Pages\CreateZanzibarPackage;
use App\Filament\Resources\ZanzibarPackages\Pages\EditZanzibarPackage;
use App\Filament\Resources\ZanzibarPackages\Pages\ListZanzibarPackages;
use App\Filament\Resources\Tours\TourResource;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class ZanzibarPackageResource extends TourResource
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSun;

    protected static ?string $navigationLabel = 'Zanzibar';

    protected static ?string $modelLabel = 'Zanzibar package';

    protected static ?string $pluralModelLabel = 'Zanzibar packages';

    protected static ?string $slug = 'zanzibar';

    protected static ?int $navigationSort = 4;

    public static function tourCategory(): string
    {
        return 'zanzibar';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListZanzibarPackages::route('/'),
            'create' => CreateZanzibarPackage::route('/create'),
            'edit' => EditZanzibarPackage::route('/{record}/edit'),
        ];
    }
}
