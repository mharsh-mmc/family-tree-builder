<?php

use App\Http\Controllers\FamilyTreeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Family Tree Routes
Route::middleware('auth')->group(function () {
    Route::get('/family-tree', [FamilyTreeController::class, 'index'])->name('family-tree.index');
    Route::post('/family-tree/node', [FamilyTreeController::class, 'store'])->name('family-tree.store');
    Route::get('/family-tree/node/{id}', [FamilyTreeController::class, 'show'])->name('family-tree.show');
    Route::patch('/family-tree/node/{id}', [FamilyTreeController::class, 'update'])->name('family-tree.update');
    Route::delete('/family-tree/node/{id}', [FamilyTreeController::class, 'destroy'])->name('family-tree.destroy');
    Route::post('/family-tree/edges', [FamilyTreeController::class, 'storeEdge'])->name('family-tree.edges.store');
    Route::delete('/family-tree/edges/{edge}', [FamilyTreeController::class, 'destroyEdge'])->name('family-tree.edges.destroy');
});

// Public Family Tree Viewer
Route::get('/family-tree/viewer/{userId}', [FamilyTreeController::class, 'viewer'])->name('family-tree.viewer');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
