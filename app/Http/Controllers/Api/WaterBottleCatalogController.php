<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WaterTypeResource;
use App\Models\WaterType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WaterBottleCatalogController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $waterTypes = WaterType::query()
            ->with([
                'compositions' => fn ($query) => $query->orderBy('bottle_size_id')->orderBy('id'),
                'compositions.ingredient:id,name,price_per_ml',
                'compositions.bottleSize:id,name,capacity_ml',
            ])
            ->orderBy('name')
            ->paginate(3);

        return WaterTypeResource::collection($waterTypes);
    }
}
