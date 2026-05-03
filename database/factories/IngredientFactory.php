<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $catalog = [
            ['name' => 'صوديوم (Na⁺)', 'min' => 0.02, 'max' => 0.06],
            ['name' => 'بوتاسيوم (K⁺)', 'min' => 0.04, 'max' => 0.09],
            ['name' => 'مغنيسيوم (Mg²⁺)', 'min' => 0.03, 'max' => 0.07],
            ['name' => 'كالسيوم (Ca²⁺)', 'min' => 0.03, 'max' => 0.06],
            ['name' => 'بيكربونات (HCO₃⁻)', 'min' => 0.02, 'max' => 0.05],
        ];

        $pick = fake()->randomElement($catalog);

        return [
            'name' => $pick['name'].' — عينة '.fake()->unique()->numerify('###'),
            'price_per_ml' => fake()->randomFloat(6, $pick['min'], $pick['max']),
        ];
    }
}
