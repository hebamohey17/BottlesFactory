<?php

namespace Database\Factories;

use App\Models\WaterType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WaterType>
 */
class WaterTypeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            'مياه معدنية طبيعية',
            'مياه معدنية غازية',
            'مياه منبع',
            'مياه معادن متوازنة',
            'مياه مقطرة معادن خفيفة',
        ];

        return [
            'name' => fake()->unique()->randomElement($names).' — '.fake()->lexify('???'),
        ];
    }
}
