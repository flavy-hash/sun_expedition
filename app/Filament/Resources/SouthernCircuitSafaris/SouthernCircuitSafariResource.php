<?php

namespace App\Filament\Resources\SouthernCircuitSafaris;

use App\Filament\Resources\SouthernCircuitSafaris\Pages\CreateSouthernCircuitSafari;
use App\Filament\Resources\SouthernCircuitSafaris\Pages\EditSouthernCircuitSafari;
use App\Filament\Resources\SouthernCircuitSafaris\Pages\ListSouthernCircuitSafaris;
use App\Filament\Resources\Tours\TourResource;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class SouthernCircuitSafariResource extends TourResource
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Southern Circuit';

    protected static ?string $modelLabel = 'Southern Circuit safari';

    protected static ?string $pluralModelLabel = 'Southern Circuit safaris';

    protected static ?string $slug = 'southern-circuit';

    protected static ?int $navigationSort = 2;

    public static function tourCategory(): string
    {
        return 'safari';
    }

    public static function tourCircuit(): ?string
    {
        return 'southern';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSouthernCircuitSafaris::route('/'),
            'create' => CreateSouthernCircuitSafari::route('/create'),
            'edit' => EditSouthernCircuitSafari::route('/{record}/edit'),
        ];
    }
}
