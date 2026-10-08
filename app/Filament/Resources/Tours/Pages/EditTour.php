<?php

namespace App\Filament\Resources\Tours\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

abstract class EditTour extends EditRecord
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewOnSite')
                ->label('View on site')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(fn (): string => route('tours.show', $this->getRecord()))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }

    // Edit URLs contain the slug, so follow the record to its new address when the slug is renamed.
    protected function getRedirectUrl(): ?string
    {
        if ($this->getRecord()->wasChanged('slug')) {
            return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
        }

        return parent::getRedirectUrl();
    }
}
