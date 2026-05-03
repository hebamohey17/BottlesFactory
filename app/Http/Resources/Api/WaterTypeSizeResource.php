<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{
 *     id: int,
 *     name: string,
 *     capacity_ml: int,
 *     ingredients_count: int,
 *     used_ml: float,
 *     remaining_ml: float,
 *     size_price: float,
 *     ingredients: array<int, array{id: int, name: string, amount_ml: float, price_per_ml: float, line_total: float}>
 * } $resource
 */
class WaterTypeSizeResource extends JsonResource
{
    /**
     * @return array<string, float|int|string|array<int, array<string, float|int|string>>>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this['id'],
            'name' => (string) $this['name'],
            'capacity_ml' => (int) $this['capacity_ml'],
            'ingredients_count' => (int) $this['ingredients_count'],
            'used_ml' => round((float) $this['used_ml'], 3),
            'remaining_ml' => round((float) $this['remaining_ml'], 3),
            'size_price' => round((float) $this['size_price'], 2),
            'ingredients' => WaterTypeSizeIngredientResource::collection($this['ingredients'])->resolve(),
        ];
    }
}
