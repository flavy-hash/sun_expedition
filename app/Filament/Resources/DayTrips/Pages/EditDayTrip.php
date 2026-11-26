<?php

namespace App\Filament\Resources\DayTrips\Pages;

use App\Filament\Resources\DayTrips\DayTripResource;
use App\Filament\Resources\Tours\Pages\EditTour;

class EditDayTrip extends EditTour
{
    protected static string $resource = DayTripResource::class;
}
