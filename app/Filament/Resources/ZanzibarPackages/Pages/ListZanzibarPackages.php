<?php

namespace App\Filament\Resources\ZanzibarPackages\Pages;

use App\Filament\Resources\ZanzibarPackages\ZanzibarPackageResource;
use App\Filament\Resources\Tours\Pages\ListTours;

class ListZanzibarPackages extends ListTours
{
    protected static string $resource = ZanzibarPackageResource::class;
}
