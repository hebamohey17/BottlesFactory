<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\BottleSizeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 * @property int $capacity_ml
 */
class BottleSize extends Model
{
    /** @use HasFactory<BottleSizeFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'capacity_ml',
    ];

    /**
     * @return HasMany<Composition, $this>
     */
    public function compositions(): HasMany
    {
        return $this->hasMany(Composition::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity_ml' => 'integer',
        ];
    }
}
