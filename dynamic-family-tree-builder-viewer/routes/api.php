<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FamilyTreeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Family Tree API Routes
Route::get('/family-tree/formats/{userId}', [FamilyTreeController::class, 'getFormats'])->name('api.family-tree.formats');
