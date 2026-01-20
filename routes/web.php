<?php

declare(strict_types=1);

use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

// List page
Route::get('/', [PetController::class, 'index'])->name('pets.index');

// Create page
Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
// @todo Add docblock for create method

// Store a new pet
Route::post('/pets', [PetController::class, 'store'])->name('pets.store');

// Edit a pet
Route::get('/pets/{id}/edit', [PetController::class, 'edit'])->name('pets.edit');

// Update a pet
Route::post('/pets/{id}', [PetController::class, 'update'])->name('pets.update');

// Delete a pet
Route::post('/pets/{id}/delete', [PetController::class, 'destroy'])->name('pets.destroy');

// Delete a pet (ajax)
Route::post('/ajax/pets/{id}/delete', [PetController::class, 'destroyAjax'])->name('pets.destroy.ajax');

// Get all pets (ajax)
Route::get('/ajax/pets', [PetController::class, 'ajaxList']);
