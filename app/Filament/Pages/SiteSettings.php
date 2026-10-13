<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SiteSettings extends Page
{
    protected string $view = 'filament.pages.site-settings';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string | UnitEnum | null $navigationGroup = 'Bookings';

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $title = 'Site settings';

    protected static ?int $navigationSort = 9;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'bank_accounts' => Setting::get('bank_accounts', []),
            'payment_instructions' => Setting::get('payment_instructions'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Bank accounts')
                    ->description('Printed on quote PDFs (two per row) with the quote reference as the payment reference. Accounts missing a bank name, account name or number are left out.')
                    ->schema([
                        Repeater::make('bank_accounts')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('bank_name')->label('Bank')->required()->maxLength(100),
                                TextInput::make('account_name')->label('Account name')->required()->maxLength(150),
                                TextInput::make('account_number')->label('Account number')->required()->maxLength(60),
                                TextInput::make('branch')->maxLength(100),
                                TextInput::make('swift')->label('SWIFT / BIC')->maxLength(20),
                                TextInput::make('currency')->maxLength(10)->placeholder('USD'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Add bank account')
                            ->itemLabel(fn (array $state): ?string => collect([$state['bank_name'] ?? null, $state['currency'] ?? null])->filter()->join(' · ') ?: null)
                            ->collapsible(),
                    ]),
                Section::make('Payment instructions')
                    ->schema([
                        Textarea::make('payment_instructions')
                            ->hiddenLabel()
                            ->rows(4)
                            ->maxLength(2000)
                            ->placeholder('e.g. Please quote your reference with every payment. Bank charges are paid by the sender.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        Setting::put('bank_accounts', array_values($state['bank_accounts'] ?? []));
        Setting::put('payment_instructions', $state['payment_instructions'] ?? null);

        Notification::make()->title('Settings saved')->success()->send();
    }
}
