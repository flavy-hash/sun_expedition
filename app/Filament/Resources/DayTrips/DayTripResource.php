<?php

namespace App\Filament\Resources\DayTrips;

use App\Filament\Resources\DayTrips\Pages\CreateDayTrip;
use App\Filament\Resources\DayTrips\Pages\EditDayTrip;
use App\Filament\Resources\DayTrips\Pages\ListDayTrips;
use App\Filament\Resources\Tours\TourResource;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class DayTripResource extends TourResource
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $navigationLabel = 'Day Trips';

    protected static ?string $modelLabel = 'Day trip';

    protected static ?string $pluralModelLabel = 'Day trips';

    protected static ?string $slug = 'day-trips';

    protected static ?int $navigationSort = 5;

    public static function tourCategory(): string
    {
        return 'day-trips';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDayTrips::route('/'),
            'create' => CreateDayTrip::route('/create'),
            'edit' => EditDayTrip::route('/{record}/edit'),
        ];
    }
}
