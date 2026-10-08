<?php

namespace App\Filament\Resources\NorthernCircuitSafaris;

use App\Filament\Resources\NorthernCircuitSafaris\Pages\CreateNorthernCircuitSafari;
use App\Filament\Resources\NorthernCircuitSafaris\Pages\EditNorthernCircuitSafari;
use App\Filament\Resources\NorthernCircuitSafaris\Pages\ListNorthernCircuitSafaris;
use App\Filament\Resources\Tours\TourResource;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class NorthernCircuitSafariResource extends TourResource
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $navigationLabel = 'Northern Circuit';

    protected static ?string $modelLabel = 'Northern Circuit safari';

    protected static ?string $pluralModelLabel = 'Northern Circuit safaris';

    protected static ?string $slug = 'northern-circuit';

    protected static ?int $navigationSort = 1;

    public static function tourCategory(): string
    {
        return 'safari';
    }

    public static function tourCircuit(): ?string
    {
        return 'northern';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNorthernCircuitSafaris::route('/'),
            'create' => CreateNorthernCircuitSafari::route('/create'),
            'edit' => EditNorthernCircuitSafari::route('/{record}/edit'),
        ];
    }
}
