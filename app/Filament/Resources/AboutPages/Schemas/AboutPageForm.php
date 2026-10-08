<?php

namespace App\Filament\Resources\AboutPages\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AboutPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page heading')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Heading')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('title_highlight')
                        ->label('Highlighted words')
                        ->helperText('Shown right after the heading, in italics.')
                        ->maxLength(255),
                    Textarea::make('intro')
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText('Shown under the heading.'),
                ]),
            Section::make('Our story')
                ->columnSpanFull()
                ->schema([
                    Textarea::make('body')
                        ->hiddenLabel()
                        ->rows(14)
                        ->helperText('Separate paragraphs with an empty line.'),
                ]),
            Section::make('Key facts')
                ->description('Shown as cards under the story (up to 4).')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('stats')
                        ->hiddenLabel()
                        ->schema([
                            TextInput::make('value')
                                ->required()
                                ->maxLength(20)
                                ->placeholder('e.g. 24h'),
                            TextInput::make('label')
                                ->label('Description')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(2)
                        ->maxItems(4)
                        ->defaultItems(0)
                        ->addActionLabel('Add fact'),
                ]),
        ]);
    }
}
