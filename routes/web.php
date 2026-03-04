<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;

// Landing page
Route::get('/', function () { 
    return view('welcome'); 
});

// Add Company Page (GET Form)
Route::get('/add-company', function () {
    return view('company.add');
})->name('company.add');

// Save Company (POST)
Route::post('/save-company', [CompanyController::class, 'store'])->name('company.save');