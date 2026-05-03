<?php

namespace App\Http\Resources\Api;

use App\Models\Composition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class WaterTypeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Collection<int, Composition> $compositions */
        $compositions = $this->compositions;

        $sizes = $compositions
            ->groupBy('bottle_size_id')
            ->map(function (Collection $sizeCompositions): array {
                /** @var Composition $first */
                $first = $sizeCompositions->first();
                $bottleSize = $first->bottleSize;

                $ingredients = $sizeCompositions->map(function (Composition $composition): array {
                    $amount = (float) $composition->amount_ml;
                    $pricePerMl = (float) ($composition->ingredient?->price_per_ml ?? 0);

                    return [
                        'id' => (int) $composition->ingredient_id,
                        'name' => (string) ($composition->ingredient?->name ?? ''),
                        'amount_ml' => $amount,
                        'price_per_ml' => $pricePerMl,
                        'line_total' => $amount * $pricePerMl,
                    ];
                })->values();

                $usedMl = (float) $ingredients->sum('amount_ml');
                $sizePrice = (float) $ingredients->sum('line_total');
                $capacityMl = (int) ($bottleSize?->capacity_ml ?? 0);

                return [
                    'id' => (int) ($bottleSize?->id ?? 0),
                    'name' => (string) ($bottleSize?->name ?? ''),
                    'capacity_ml' => $capacityMl,
                    'ingredients_count' => $ingredients->count(),
                    'used_ml' => $usedMl,
                    'remaining_ml' => max(0.0, $capacityMl - $usedMl),
                    'size_price' => $sizePrice,
                    'ingredients' => $ingredients->all(),
                ];
            })
            ->sortBy('capacity_ml')
            ->values();

        $totalPrice = (float) $sizes->sum('size_price');

        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'sizes' => WaterTypeSizeResource::collection($sizes)->resolve(),
        ];
    }
}
