<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Support\SiteImageUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->alphaDash()
                            ->unique(ignoreRecord: true)
                            ->helperText('The page address: /journal/this-slug'),
                        Textarea::make('excerpt')
                            ->required()
                            ->maxLength(255)
                            ->rows(2)
                            ->helperText('One or two sentences shown on journal cards.'),
                        Textarea::make('body')
                            ->label('Article')
                            ->required()
                            ->rows(18)
                            ->helperText('Separate paragraphs with an empty line.'),
                    ]),
                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        DateTimePicker::make('published_at')
                            ->label('Publish date')
                            ->seconds(false)
                            ->helperText('Leave empty to keep this post as a draft. A future date schedules it.'),
                        TextInput::make('author_name')
                            ->label('Author')
                            ->required()
                            ->maxLength(255)
                            ->default('Suni Expedition Team'),
                        SiteImageUpload::make('cover_image', 'journal')
                            ->label('Cover photo'),
                    ]),
            ]);
    }
}
