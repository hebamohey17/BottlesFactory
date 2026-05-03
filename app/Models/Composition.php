<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\CompositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $water_type_id
 * @property int $bottle_size_id
 * @property int $ingredient_id
 * @property string $amount_ml
 */
class Composition extends Model
{
    /** @use HasFactory<CompositionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'water_type_id',
        'bottle_size_id',
        'ingredient_id',
        'amount_ml',
    ];

    /**
     * @return BelongsTo<WaterType, $this>
     */
    public function waterType(): BelongsTo
    {
        return $this->belongsTo(WaterType::class);
    }

    /**
     * @return BelongsTo<BottleSize, $this>
     */
    public function bottleSize(): BelongsTo
    {
        return $this->belongsTo(BottleSize::class);
    }

    /**
     * @return BelongsTo<Ingredient, $this>
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount_ml' => 'decimal:3',
        ];
    }
}
