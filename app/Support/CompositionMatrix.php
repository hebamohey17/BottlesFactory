<?php

namespace App\Support;

use App\Models\BottleSize;
use App\Models\Composition;
use App\Models\Ingredient;
use App\Models\WaterType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CompositionMatrix
{
    /**
     * @param  array<int, array<string, mixed>>  $groups
     */
    public static function validate(array $groups): void
    {
        $errors = [];

        $usedBottleSizes = [];

        foreach ($groups as $index => $group) {
            $prefix = "composition_matrix.{$index}";

            $bottleSizeId = $group['bottle_size_id'] ?? null;
            $lines = $group['lines'] ?? [];

            if ($bottleSizeId === null || $bottleSizeId === '') {
                if (self::linesHaveContent($lines)) {
                    $errors["{$prefix}.bottle_size_id"] = __('يجب اختيار حجم الزجاجة لهذه التركيبة.');
                }

                continue;
            }

            if (isset($usedBottleSizes[$bottleSizeId])) {
                $errors["{$prefix}.bottle_size_id"] = __('لا تكرر نفس حجم الزجاجة أكثر من مرة.');
            }
            $usedBottleSizes[$bottleSizeId] = true;

            $bottleSize = BottleSize::query()->find($bottleSizeId);
            if ($bottleSize === null) {
                $errors["{$prefix}.bottle_size_id"] = __('حجم الزجاجة غير صالح.');

                continue;
            }

            $ingredientIds = [];
            $totalMl = 0.0;

            foreach ($lines as $lineIndex => $line) {
                $linePrefix = "{$prefix}.lines.{$lineIndex}";
                $ingredientId = $line['ingredient_id'] ?? null;
                $amountRaw = $line['amount_ml'] ?? null;

                if ($ingredientId === null || $ingredientId === '' || $amountRaw === null || $amountRaw === '') {
                    continue;
                }

                $ingredient = Ingredient::query()->find($ingredientId);
                if ($ingredient === null) {
                    $errors["{$linePrefix}.ingredient_id"] = __('العنصر غير صالح.');

                    continue;
                }

                if (! is_numeric($amountRaw)) {
                    $errors["{$linePrefix}.amount_ml"] = __('الكمية يجب أن تكون رقماً.');

                    continue;
                }

                $amount = (float) $amountRaw;
                if ($amount <= 0) {
                    $errors["{$linePrefix}.amount_ml"] = __('الكمية يجب أن تكون أكبر من صفر.');

                    continue;
                }

                if (isset($ingredientIds[$ingredientId])) {
                    $errors["{$linePrefix}.ingredient_id"] = __('لا تكرر نفس العنصر مرتين لنفس الحجم.');
                }
                $ingredientIds[$ingredientId] = true;

                $totalMl += $amount;
            }

            if ($totalMl > (float) $bottleSize->capacity_ml) {
                $errors["{$prefix}.lines"] = __('مجموع العناصر (:total مل) يتجاوز سعة الزجاجة (:capacity مل).', [
                    'total' => rtrim(rtrim(number_format($totalMl, 3, '.', ''), '0'), '.'),
                    'capacity' => $bottleSize->capacity_ml,
                ]);
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * @return array<int, array{bottle_size_id: int, lines: array<int, array{ingredient_id: int, amount_ml: float}>}>
     */
    public static function hydrateFromWaterType(WaterType $waterType): array
    {
        $compositions = $waterType->compositions()
            ->orderBy('bottle_size_id')
            ->orderBy('id')
            ->get();

        /** @var array<int, array{bottle_size_id: int, lines: array<int, array{ingredient_id: int, amount_ml: float}>}> $groups */
        $groups = [];

        foreach ($compositions as $row) {
            $sizeId = (int) $row->bottle_size_id;
            if (! isset($groups[$sizeId])) {
                $groups[$sizeId] = [
                    'bottle_size_id' => $sizeId,
                    'lines' => [],
                ];
            }

            $groups[$sizeId]['lines'][] = [
                'ingredient_id' => (int) $row->ingredient_id,
                'amount_ml' => (float) $row->amount_ml,
            ];
        }

        return array_values($groups);
    }

    /**
     * @param  array<int, array<string, mixed>>  $groups
     */
    public static function sync(WaterType $waterType, array $groups): void
    {
        DB::transaction(function () use ($waterType, $groups): void {
            Composition::query()->where('water_type_id', $waterType->id)->delete();

            foreach ($groups as $group) {
                $bottleSizeId = $group['bottle_size_id'] ?? null;
                if ($bottleSizeId === null || $bottleSizeId === '') {
                    continue;
                }

                foreach ($group['lines'] ?? [] as $line) {
                    $ingredientId = $line['ingredient_id'] ?? null;
                    $amountRaw = $line['amount_ml'] ?? null;

                    if ($ingredientId === null || $ingredientId === '' || $amountRaw === null || $amountRaw === '') {
                        continue;
                    }

                    $amount = (float) $amountRaw;
                    if ($amount <= 0) {
                        continue;
                    }

                    Composition::query()->create([
                        'water_type_id' => $waterType->id,
                        'bottle_size_id' => (int) $bottleSizeId,
                        'ingredient_id' => (int) $ingredientId,
                        'amount_ml' => $amount,
                    ]);
                }
            }
        });
    }

    /**
     * @param  array<int, mixed>  $lines
     */
    private static function linesHaveContent(array $lines): bool
    {
        foreach ($lines as $line) {
            if (! is_array($line)) {
                continue;
            }

            $ingredientId = $line['ingredient_id'] ?? null;
            $amountRaw = $line['amount_ml'] ?? null;

            if (($ingredientId !== null && $ingredientId !== '') || ($amountRaw !== null && $amountRaw !== '')) {
                return true;
            }
        }

        return false;
    }
}
