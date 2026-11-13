<?php

use App\Http\Controllers\BukuController;
use Illuminate\Support\Facades\Route;

Route::resource('buku', BukuController::class)->except('show');
Route::get('/buku/{kode}', [BukuController::class, 'show'])->name('buku.show');
