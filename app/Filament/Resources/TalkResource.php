<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\RelationManagers;
use App\Models\Talk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('abstract')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('speaker_id')
                    ->relationship('speaker', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filters');
            })
            ->persistSearchInSession()
            ->toggleColumnsTriggerAction(function ($action) {
                return $action->button()->label('columns');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('abstract')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('speaker.avatar')
                    ->collection('speaker-avatar')
                    ->label('Speaker Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=FFF&name=' . urlencode($record->speaker->name);
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->label('Speaker Name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (Talk $record) {
                        return $record->speaker->name . ' ' . $record->speaker->email;
                    })
                    ->toggleable(),
                Tables\Columns\ToggleColumn::make('new_talk')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(function ($state) {
                        return $state->getColor();
                    })
                    ->toggleable(),
                Tables\Columns\IconColumn::make('length')
                    ->icon(function ($state) {
                        return match ($state) {
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::LIGHTENING => 'heroicon-o-bolt',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                        };
                    })->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('new_talk'),
                Tables\Filters\SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->searchable()
                    ->multiple(),
                Tables\Filters\Filter::make('has_avatar')
                    ->label('Has Avatar')
                    ->toggle()
                    ->query(function ($query) {
                        return $query->whereHas('speaker', function (Builder $query) {
                            $query->whereHas('media');
                        });
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Talk $record) {
                            $record->approve();
                        })
                        ->after(function () {
                            Notification::make()->success()->title('This talk was approved')
                                ->body('The speaker has been notified and the talk has been added to the conference schedule.')
                                ->send();
                        })
                        ->visible(function ($record) {
                            return $record->status === TalkStatus::SUBMITTED;
                        }),
                    Tables\Actions\Action::make(name: 'reject')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Talk $record) {
                            $record->reject();
                        })
                        ->after(function () {
                            Notification::make()->danger()->title('This talk was reject')
                                ->body('The speaker has been notified.')
                                ->send();
                        })
                        ->visible(function ($record) {
                            return $record->status === TalkStatus::SUBMITTED;
                        }),
                ]),
            ],)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            // 'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
