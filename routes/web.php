<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FamilyTreeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
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
Route::prefix('family-tree')->name('family-tree.')->group(function () {
    Route::get('/', [FamilyTreeController::class, 'index'])->name('index');
    
    // Node (Family Member) routes
    Route::post('/nodes', [FamilyTreeController::class, 'storeMember'])->name('nodes.store');
    Route::put('/nodes/{member}', [FamilyTreeController::class, 'updateMember'])->name('nodes.update');
    Route::delete('/nodes/{member}', [FamilyTreeController::class, 'destroyMember'])->name('nodes.destroy');
    
    // Edge (Family Connection) routes
    Route::post('/edges', [FamilyTreeController::class, 'storeConnection'])->name('edges.store');
    Route::delete('/edges/{connection}', [FamilyTreeController::class, 'destroyConnection'])->name('edges.destroy');
    
    // Utility routes
    Route::post('/positions', [FamilyTreeController::class, 'updatePositions'])->name('positions.update');
    Route::get('/export', [FamilyTreeController::class, 'export'])->name('export');
    Route::post('/import', [FamilyTreeController::class, 'import'])->name('import');
    Route::post('/reset', [FamilyTreeController::class, 'reset'])->name('reset');
});

require __DIR__.'/auth.php';