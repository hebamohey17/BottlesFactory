<?php

use App\Http\Controllers\Api\WaterBottleCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/water-bottles', [WaterBottleCatalogController::class, 'index']);
