<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonController;

Route::prefix('v1')->group(function () {
    Route::get('/people/recommended', [PersonController::class, 'recommended']);
    Route::post('/people/{person}/like', [PersonController::class, 'like']);
    Route::post('/people/{person}/dislike', [PersonController::class, 'dislike']);
    Route::get('/people/liked', [PersonController::class, 'likedPeople']);
});

