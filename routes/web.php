<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\BlockchainController;
use App\Http\Controllers\ReportController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ===== Master Data =====
Route::prefix('master')->group(function () {
    Route::resource('items', ItemController::class)->only(['index']);
    Route::resource('batches', BatchController::class)->only(['index']);
    // Jika ada lokasi/gudang: Route::resource('locations', LocationController::class)->only(['index']);
});

// ===== Pengadaan =====
Route::prefix('procurement')->group(function () {
    Route::get('/', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::get('/create', [ProcurementController::class, 'create'])->name('procurement.create');
    Route::post('/', [ProcurementController::class, 'store'])->name('procurement.store');
    Route::get('/{po}', [ProcurementController::class, 'show'])->name('procurement.show');
    Route::get('/{po}/trace', [ProcurementController::class, 'trace'])->name('procurement.trace');
    Route::get('/{po}/edit', [ProcurementController::class, 'edit'])->name('procurement.edit');
    Route::put('/{po}', [ProcurementController::class, 'update'])->name('procurement.update');
    Route::delete('/{po}', [ProcurementController::class, 'destroy'])->name('procurement.destroy');
    Route::get('/{order}/pdf-preview', [ProcurementController::class, 'previewPdf'])->name('procurement.previewPdf');
    Route::get('/{order}/pdf', [ProcurementController::class, 'downloadPdf'])->name('procurement.downloadPdf');

});


// ===== Inventory =====
Route::prefix('inventory')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');      // Daftar barang
    Route::get('/create', [InventoryController::class, 'create'])->name('inventory.create'); // Form tambah barang
    Route::post('/', [InventoryController::class, 'store'])->name('inventory.store');        // Proses tambah barang
    Route::get('/{item}/edit', [InventoryController::class, 'edit'])->name('inventory.edit'); // Form edit barang
    Route::put('/{item}', [InventoryController::class, 'update'])->name('inventory.update');  // Proses edit barang
    Route::delete('/{item}', [InventoryController::class, 'destroy'])->name('inventory.destroy'); // Hapus barang
    Route::get('/mutation/{id}', [InventoryController::class, 'mutation'])->name('inventory.mutation');
    // Contoh mutasi stok (bisa dikembangkan nanti)
    // Route::get('/mutasi', [InventoryController::class, 'mutation'])->name('inventory.mutation');
});


// ===== Penjualan =====
Route::prefix('sales')->group(function () {
    // Halaman daftar sales order
    Route::get('/', [SalesController::class, 'index'])->name('sales.index');
    
    // Halaman tambah sales order
    Route::get('/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/', [SalesController::class, 'store'])->name('sales.store');
    
    // Detail sales order
    Route::get('/{id}', [SalesController::class, 'show'])->name('sales.show');
    
    // Edit sales order
    Route::get('/{id}/edit', [SalesController::class, 'edit'])->name('sales.edit');
    Route::put('/{id}', [SalesController::class, 'update'])->name('sales.update');
    
    // (Opsional) Hapus sales order
    Route::delete('/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');
});

// ===== Blockchain & Traceability =====
Route::prefix('blockchain')->group(function () {
   
    Route::get('/{block}/verify', [BlockchainController::class, 'verify'])->name('blockchain.verify');
    // List semua block dalam bentuk card
    Route::get('/', [BlockchainController::class, 'index'])->name('blockchain.index');
    // Detail satu block (dari tombol detail di card)
    Route::get('/{block}', [BlockchainController::class, 'show'])->name('blockchain.detail');

});

// ===== Laporan =====
Route::prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
});

// ===== Auth (Logout) =====
Route::post('logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');
