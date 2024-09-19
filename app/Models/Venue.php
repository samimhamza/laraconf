<?php

namespace App\Models;

use App\Enums\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms;

class Venue extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'region' => Region::class
    ];

    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class);
    }

    public static function getForm(string $region = null)
    {
        $form = [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('city')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('country')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('postal_code')
                ->required()
                ->maxLength(255),
        ];
        if ($region) {
            $form[] = Forms\Components\Hidden::make('region')
                ->default($region);
        } else {
            $form[] = Forms\Components\Select::make('region')
                ->native(false)
                ->enum(Region::class)
                ->options(Region::class);
        }
        return $form;
    }
}
