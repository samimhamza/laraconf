<?php

namespace Database\Factories;

use App\Enums\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Conference;
use App\Models\Venue;

class ConferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Conference::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $region = $this->faker->randomElement(Region::cases());
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'start_date' => $this->faker->dateTime(),
            'end_date' => $this->faker->dateTime(),
            'status' => $this->faker->randomElement([
                'draft',
                'published',
                'archived'
            ]),
            'is_published' => $this->faker->randomElement([0, 1]),
            'region' => $region,
            'venue_id' => Venue::where('region', $region)->inRandomOrder()->first(),
        ];
    }
}
