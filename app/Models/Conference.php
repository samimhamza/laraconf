<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'venue_id' => 'integer',
        'region' => Region::class
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }


    public static function getForm()
    {
        return [
            Section::make('Conference Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Conference Name')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(64),
                    MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->required(),
                    DateTimePicker::make('start_date')
                        ->native(false)
                        ->required(),
                    DateTimePicker::make('end_date')
                        ->native(false)
                        ->required(),
                    Fieldset::make('Status')
                        ->columns(2)

                        ->schema([
                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'archived' => 'Archived'
                                ])
                                ->required()
                                ->native(false),

                            Checkbox::make('is_published')
                                ->default(false),
                        ])
                ]),
            Section::make('Location')
                ->columns(2)
                ->schema([
                    Select::make('region')
                        ->live()
                        ->enum(Region::class)
                        ->options(Region::class)
                        ->native(false),
                    Select::make('venue_id')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(function (Get $get) {
                            return Venue::getForm(
                                $get('region')
                            );
                        })
                        ->editOptionForm(function (Get $get) {
                            return Venue::getForm(
                                $get('region')
                            );
                        })
                        ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                            return $query->where('region', $get('region'));
                        }),
                ]),
            // CheckboxList::make('speakers')
            //     ->relationship('speakers', 'name')
            //     ->options(Speaker::all()->pluck('name', 'id')),

            Actions::make([
                Action::make('star')
                    ->label('Fill with Factory Data')
                    ->icon('heroicon-m-star')
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    })
                    ->visible(function (string $operation) {
                        if ($operation != 'create') {
                            return false;
                        }
                        if (app()->environment('local')) {
                            return true;
                        }
                        return false;
                    }),

            ]),
        ];
    }
}
