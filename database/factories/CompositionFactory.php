<?php

namespace Database\Factories;

use App\Models\BottleSize;
use App\Models\Composition;
use App\Models\Ingredient;
use App\Models\WaterType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Composition>
 */
class CompositionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'water_type_id' => WaterType::factory(),
            'bottle_size_id' => BottleSize::factory(),
            'ingredient_id' => Ingredient::factory(),
            'amount_ml' => fake()->randomFloat(3, 1, 50),
        ];
    }
}
