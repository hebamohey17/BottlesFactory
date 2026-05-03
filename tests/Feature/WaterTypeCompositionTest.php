<?php

namespace Tests\Feature;

use App\Models\BottleSize;
use App\Models\Ingredient;
use App\Models\WaterType;
use App\Support\CompositionMatrix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WaterTypeCompositionTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_price_matches_formula(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Test Ion',
            'price_per_ml' => '2.000000',
        ]);

        $size = BottleSize::factory()->create([
            'name' => '100 مل',
            'capacity_ml' => 100,
        ]);

        $water = WaterType::factory()->create(['name' => 'Test Water']);

        CompositionMatrix::sync($water, [
            [
                'bottle_size_id' => $size->id,
                'lines' => [
                    ['ingredient_id' => $ingredient->id, 'amount_ml' => 10],
                ],
            ],
        ]);

        $this->assertSame(20.0, $water->fresh()->totalCompositionPrice());
    }

    public function test_validation_fails_when_amounts_exceed_bottle_capacity(): void
    {
        $a = Ingredient::factory()->create(['price_per_ml' => '1.000000']);
        $b = Ingredient::factory()->create(['price_per_ml' => '1.000000']);

        $size = BottleSize::factory()->create([
            'capacity_ml' => 100,
        ]);

        $this->expectException(ValidationException::class);

        CompositionMatrix::validate([
            [
                'bottle_size_id' => $size->id,
                'lines' => [
                    ['ingredient_id' => $a->id, 'amount_ml' => 60],
                    ['ingredient_id' => $b->id, 'amount_ml' => 50],
                ],
            ],
        ]);
    }

    public function test_validation_fails_on_duplicate_bottle_size_rows(): void
    {
        $ingredient = Ingredient::factory()->create();
        $size = BottleSize::factory()->create(['capacity_ml' => 500]);

        $this->expectException(ValidationException::class);

        CompositionMatrix::validate([
            [
                'bottle_size_id' => $size->id,
                'lines' => [
                    ['ingredient_id' => $ingredient->id, 'amount_ml' => 10],
                ],
            ],
            [
                'bottle_size_id' => $size->id,
                'lines' => [
                    ['ingredient_id' => $ingredient->id, 'amount_ml' => 10],
                ],
            ],
        ]);
    }
}
