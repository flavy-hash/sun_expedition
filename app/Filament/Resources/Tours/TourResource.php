<?php

namespace App\Filament\Resources\Tours;

use App\Filament\Resources\Tours\Schemas\TourForm;
use App\Filament\Resources\Tours\Tables\ToursTable;
use App\Models\Tour;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Each admin "package" section manages one slice of the tours table: a category, optionally narrowed
 * to a safari circuit. Scoping the query also stops one section from opening another section's records by URL.
 */
abstract class TourResource extends Resource
{
    protected static ?string $model = Tour::class;

    protected static string | UnitEnum | null $navigationGroup = 'Packages';

    protected static ?string $recordTitleAttribute = 'title';

    abstract public static function tourCategory(): string;

    public static function tourCircuit(): ?string
    {
        return null;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('category', static::tourCategory())
            ->when(static::tourCircuit(), fn (Builder $query, string $circuit) => $query->where('circuit', $circuit));
    }

    public static function form(Schema $schema): Schema
    {
        return TourForm::configure($schema, static::tourCategory(), static::tourCircuit());
    }

    public static function table(Table $table): Table
    {
        return ToursTable::configure($table, static::tourCategory());
    }
}
