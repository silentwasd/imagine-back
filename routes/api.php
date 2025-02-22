<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\TelegramCollectorController;
use App\Http\Controllers\TelegramGalleryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('images', ImageController::class);

Route::any('telegram/gallery', TelegramGalleryController::class);
Route::any('telegram/collector', TelegramCollectorController::class);
