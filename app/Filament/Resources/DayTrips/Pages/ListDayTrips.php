<?php

namespace App\Filament\Resources\DayTrips\Pages;

use App\Filament\Resources\DayTrips\DayTripResource;
use App\Filament\Resources\Tours\Pages\ListTours;

class ListDayTrips extends ListTours
{
    protected static string $resource = DayTripResource::class;
}
