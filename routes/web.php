<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\EstimatorController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
Route::get('/estimate/{category:slug}', [EstimatorController::class, 'show'])->name('estimator');
Route::post('/estimate/calculate', [EstimatorController::class, 'calculate'])->name('estimation.calculate');
Route::get('/clinic-finder', [ClinicController::class, 'index'])->name('clinic.finder');
Route::get('/clinic-search', [ClinicController::class, 'search'])->name('clinic.search');
