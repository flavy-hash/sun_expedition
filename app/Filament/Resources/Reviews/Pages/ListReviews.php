<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Testimonial;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add a review'),
        ];
    }

    public function getTabs(): array
    {
        $pending = Testimonial::where('is_approved', false)->count();

        return [
            'pending' => Tab::make('Awaiting approval')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_approved', false))
                ->badge($pending ?: null)
                ->badgeColor('warning'),
            'published' => Tab::make('Published')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_approved', true)),
            'all' => Tab::make('All'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return Testimonial::where('is_approved', false)->exists() ? 'pending' : 'published';
    }
}
