<?php

namespace App\Filament\Resources\KilimanjaroPackages;

use App\Filament\Resources\KilimanjaroPackages\Pages\CreateKilimanjaroPackage;
use App\Filament\Resources\KilimanjaroPackages\Pages\EditKilimanjaroPackage;
use App\Filament\Resources\KilimanjaroPackages\Pages\ListKilimanjaroPackages;
use App\Filament\Resources\Tours\TourResource;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class KilimanjaroPackageResource extends TourResource
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Kilimanjaro';

    protected static ?string $modelLabel = 'Kilimanjaro package';

    protected static ?string $pluralModelLabel = 'Kilimanjaro packages';

    protected static ?string $slug = 'kilimanjaro';

    protected static ?int $navigationSort = 3;

    public static function tourCategory(): string
    {
        return 'kilimanjaro';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKilimanjaroPackages::route('/'),
            'create' => CreateKilimanjaroPackage::route('/create'),
            'edit' => EditKilimanjaroPackage::route('/{record}/edit'),
        ];
    }
}
