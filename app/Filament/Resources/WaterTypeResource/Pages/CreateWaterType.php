<?php

namespace App\Filament\Resources\WaterTypeResource\Pages;

use App\Filament\Resources\WaterTypeResource;
use App\Support\CompositionMatrix;
use Filament\Resources\Pages\CreateRecord;

class CreateWaterType extends CreateRecord
{
    protected static string $resource = WaterTypeResource::class;

    /**
     * @var list<int, array<string, mixed>>
     */
    protected array $pendingCompositionMatrix = [];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $matrix = $data['composition_matrix'] ?? [];
        CompositionMatrix::validate($matrix);
        $this->pendingCompositionMatrix = $matrix;
        unset($data['composition_matrix']);

        return $data;
    }

    protected function afterCreate(): void
    {
        CompositionMatrix::sync($this->record, $this->pendingCompositionMatrix);
    }
}
