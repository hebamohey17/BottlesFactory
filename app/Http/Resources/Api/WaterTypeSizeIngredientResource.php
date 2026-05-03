<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{id: int, name: string, amount_ml: float, price_per_ml: float, line_total: float} $resource
 */
class WaterTypeSizeIngredientResource extends JsonResource
{
    /**
     * @return array<string, float|int|string>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this['id'],
            'name' => (string) $this['name'],
            'amount_ml' => round((float) $this['amount_ml'], 3),
            'price_per_ml' => round((float) $this['price_per_ml'], 2),
            'line_total' => round((float) $this['line_total'], 2),
        ];
    }
}
