<?php

namespace App\Filament\Resources\Contents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('page')
                    ->required(),
                TextInput::make('section')
                    ->required(),
                TextInput::make('key')
                    ->required(),
                Textarea::make('value')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
