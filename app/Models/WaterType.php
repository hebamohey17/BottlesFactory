<?php

namespace App\Models;

use Database\Factories\WaterTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property-read int $id
 * @property string $name
 */
class WaterType extends Model
{
    /** @use HasFactory<WaterTypeFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<Composition, $this>
     */
    public function compositions(): HasMany
    {
        return $this->hasMany(Composition::class);
    }

    /**
     * Total recipe cost across all bottle sizes and lines: Σ(amount_ml × price_per_ml).
     */
    public function totalCompositionPrice(): float
    {
        $value = $this->compositions()
            ->join('ingredients', 'ingredients.id', '=', 'compositions.ingredient_id')
            ->sum(DB::raw('CAST(compositions.amount_ml AS DECIMAL(18,6)) * CAST(ingredients.price_per_ml AS DECIMAL(18,6))'));

        return (float) $value;
    }


    /**
     * Cost breakdown per bottle size based on: Σ(amount_ml × price_per_ml) for each size.
     *
     * @return array<int, array{size_name: string, capacity_ml: int, cost: float}>
     */
    public function compositionCostsBySize(): array
    {
        $rows = $this->compositions()
            ->join('ingredients', 'ingredients.id', '=', 'compositions.ingredient_id')
            ->join('bottle_sizes', 'bottle_sizes.id', '=', 'compositions.bottle_size_id')
            ->selectRaw(
                'bottle_sizes.name as size_name, bottle_sizes.capacity_ml as capacity_ml, ' .
                'SUM(CAST(compositions.amount_ml AS DECIMAL(18,6)) * CAST(ingredients.price_per_ml AS DECIMAL(18,6))) as size_cost'
            )
            ->groupBy('bottle_sizes.id', 'bottle_sizes.name', 'bottle_sizes.capacity_ml')
            ->orderBy('bottle_sizes.capacity_ml')
            ->get();

        return $rows
            ->map(fn (object $row): array => [
                'size_name' => (string) $row->size_name,
                'capacity_ml' => (int) $row->capacity_ml,
                'cost' => (float) $row->size_cost,
            ])
            ->all();
    }

    public function compositionCostsBySizeFormatted(): string
    {
        $lines = collect($this->compositionCostsBySize())
            ->map(
                fn (array $row): string => "{$row['size_name']}\t" . number_format((float) $row['cost'], 2) . ' ج.م'
            );

        return $lines->isEmpty() ? '-' : $lines->implode("\n");
    }

    /**
     * @return array<int, array{
     *     size_name: string,
     *     capacity_ml: int,
     *     used_ml: float,
     *     remaining_ml: float,
     *     ingredients_count: int,
     *     ingredients: array<int, array{name: string, amount_ml: float, price_per_ml: float, line_total: float}>
     * }>
     */
    public function compositionDetailsBySize(): array
    {
        $compositions = $this->compositions()
            ->with(['ingredient:id,name,price_per_ml', 'bottleSize:id,name,capacity_ml'])
            ->orderBy('bottle_size_id')
            ->orderBy('id')
            ->get();

        /** @var array<int, array{
         *     size_name: string,
         *     capacity_ml: int,
         *     used_ml: float,
         *     remaining_ml: float,
         *     ingredients_count: int,
         *     ingredients: array<int, array{name: string, amount_ml: float, price_per_ml: float, line_total: float}>
         * }> $grouped
         */
        $grouped = [];

        foreach ($compositions as $composition) {
            if ($composition->bottleSize === null || $composition->ingredient === null) {
                continue;
            }

            $sizeId = (int) $composition->bottle_size_id;

            if (! isset($grouped[$sizeId])) {
                $grouped[$sizeId] = [
                    'size_name' => (string) $composition->bottleSize->name,
                    'capacity_ml' => (int) $composition->bottleSize->capacity_ml,
                    'used_ml' => 0.0,
                    'remaining_ml' => (float) $composition->bottleSize->capacity_ml,
                    'ingredients_count' => 0,
                    'ingredients' => [],
                ];
            }

            $amount = $composition->amount_ml;
            $pricePerMl = $composition->ingredient->price_per_ml;

            $grouped[$sizeId]['ingredients'][] = [
                'name' => (string) $composition->ingredient->name,
                'amount_ml' => $amount,
                'price_per_ml' => $pricePerMl,
                'line_total' => $amount * $pricePerMl,
            ];
            $grouped[$sizeId]['used_ml'] += $amount;
        }

        foreach ($grouped as &$row) {
            $row['ingredients_count'] = count($row['ingredients']);
            $row['remaining_ml'] = max(0.0, (float) $row['capacity_ml'] - (float) $row['used_ml']);
        }
        unset($row);

        return array_values($grouped);
    }

    public function compositionDetailsBySizeFormatted(): string
    {
        $details = collect($this->compositionDetailsBySize())
            ->map(function (array $sizeRow): string {
                $header = "{$sizeRow['size_name']} ({$sizeRow['capacity_ml']} مل) - {$sizeRow['ingredients_count']} عنصر";
                $ingredientLines = collect($sizeRow['ingredients'])
                    ->map(
                        fn (array $ingredient): string => ". {$ingredient['name']}: "
                            . number_format((float) $ingredient['amount_ml'], 3)
                            . " مل × "
                            . number_format((float) $ingredient['price_per_ml'], 2)
                            . " = "
                            . number_format((float) $ingredient['line_total'], 2)
                    )
                    ->implode("\n");

                return trim($header . "\n" . $ingredientLines);
            });

        return $details->isEmpty() ? '-' : $details->implode("\n\n");
    }
}
