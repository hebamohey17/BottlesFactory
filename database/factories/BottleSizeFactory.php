<?php

namespace Database\Factories;

use App\Models\BottleSize;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BottleSize>
 */
class BottleSizeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $packs = [
            ['name' => '250 مل — عبوة صغيرة', 'capacity_ml' => 250],
            ['name' => '330 مل — قنينة قياسية', 'capacity_ml' => 330],
            ['name' => '500 مل — عبوة متوسطة', 'capacity_ml' => 500],
            ['name' => '600 مل — عبوة رياضية', 'capacity_ml' => 600],
            ['name' => '750 مل — عبوة كبيرة', 'capacity_ml' => 750],
            ['name' => '1 لتر', 'capacity_ml' => 1000],
            ['name' => '1.5 لتر — عائلي', 'capacity_ml' => 1500],
        ];

        return fake()->randomElement($packs);
    }
}
