<?php

namespace Database\Factories;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Speaker;
use App\Models\Talk;

class TalkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Talk::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'abstract' => $this->faker->text(),
            'status' => $this->faker->randomElement(TalkStatus::class),
            'length' => $this->faker->randomElement(TalkLength::class),
            'new_talk' => $this->faker->randomElement([0, 1]),
            'speaker_id' => Speaker::factory(),
        ];
    }
}
