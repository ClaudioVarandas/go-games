<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Models\Game;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('igdb_id')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true), // Add unique validation
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true), // Add unique validation
                TextInput::make('cover_url')
                    ->url(), // Validate as URL
                DatePicker::make('release_date'),
                Textarea::make('summary')
                    ->columnSpanFull(),
                TextInput::make('rating')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100), // Assuming rating is 0-100
                TagsInput::make('genres') // Use TagsInput for array
                    ->columnSpanFull(),
                TagsInput::make('platforms') // Use TagsInput for array
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_url') // Use ImageColumn
                    ->label('Cover')
                    ->square(), // Make it square
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('release_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('igdb_id') // Keep for searching/filtering
                    ->numeric()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hide by default
                TextColumn::make('slug') // Keep for searching/filtering
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hide by default
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Add filters if needed later
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(), // Add delete action
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
