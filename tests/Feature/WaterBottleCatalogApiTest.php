<?php

namespace Tests\Feature;

use App\Models\BottleSize;
use App\Models\Ingredient;
use App\Models\WaterType;
use App\Support\CompositionMatrix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaterBottleCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_water_types_with_sizes_ingredients_and_prices(): void
    {
        $waterType = WaterType::factory()->create(['name' => 'Mineral']);
        $size500 = BottleSize::factory()->create(['name' => '500 ml', 'capacity_ml' => 500]);
        $sodium = Ingredient::factory()->create(['name' => 'Sodium', 'price_per_ml' => '2.00']);
        $potassium = Ingredient::factory()->create(['name' => 'Potassium', 'price_per_ml' => '3.00']);

        CompositionMatrix::sync($waterType, [
            [
                'bottle_size_id' => $size500->id,
                'lines' => [
                    ['ingredient_id' => $sodium->id, 'amount_ml' => 100],
                    ['ingredient_id' => $potassium->id, 'amount_ml' => 50],
                ],
            ],
        ]);

        $response = $this->getJson('/api/water-bottles');

        $response->assertOk();
        $response->assertJsonPath('data.0.name', 'Mineral');
        $response->assertJsonPath('data.0.sizes.0.name', '500 ml');
        $response->assertJsonPath('data.0.sizes.0.ingredients_count', 2);
        $response->assertJsonPath('data.0.sizes.0.used_ml', 150);
        $response->assertJsonPath('data.0.sizes.0.size_price', 350);
        $response->assertJsonPath('data.0.total_price', 350);
    }

    public function test_price_update_is_reflected_immediately_in_api(): void
    {
        $waterType = WaterType::factory()->create(['name' => 'Sparkling']);
        $size330 = BottleSize::factory()->create(['name' => '330 ml', 'capacity_ml' => 330]);
        $ingredient = Ingredient::factory()->create(['name' => 'Sodium', 'price_per_ml' => '1.00']);

        CompositionMatrix::sync($waterType, [
            [
                'bottle_size_id' => $size330->id,
                'lines' => [
                    ['ingredient_id' => $ingredient->id, 'amount_ml' => 100],
                ],
            ],
        ]);

        $this->getJson('/api/water-bottles')
            ->assertOk()
            ->assertJsonPath('data.0.total_price', 100);

        $ingredient->update(['price_per_ml' => '2.50']);

        $this->getJson('/api/water-bottles')
            ->assertOk()
            ->assertJsonPath('data.0.total_price', 250);
    }
}
