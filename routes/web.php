<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DestinationController;

// Landing page
Route::get('/', function () { 
    return view('welcome'); 
});

// Add Company Page (GET Form)
Route::get('/add-company', function () {
    return view('company.add');
})->name('company.add');

Route::post('/save-company', [CompanyController::class,'store'])->name('company.save');

Route::put('/company/{id}', [CompanyController::class,'update'])->name('company.update');

Route::delete('/company/{id}', [CompanyController::class,'destroy'])->name('company.delete');

Route::get('/add-company', [CompanyController::class,'create'])->name('company.add');



Route::get('/destinations',[DestinationController::class,'index'])->name('destination.index');

Route::post('/destination',[DestinationController::class,'store'])->name('destination.save');

Route::put('/destination/{id}',[DestinationController::class,'update'])->name('destination.update');

Route::delete('/destination/{id}',[DestinationController::class,'destroy'])->name('destination.delete');


use App\Http\Controllers\TruckController;

Route::get('/trucks',[TruckController::class,'index'])->name('truck.index');

Route::post('/truck',[TruckController::class,'store'])->name('truck.save');

Route::put('/truck/{id}',[TruckController::class,'update'])->name('truck.update');

Route::delete('/truck/{id}',[TruckController::class,'destroy'])->name('truck.delete');


use App\Http\Controllers\EmployeeController;
Route::get('/employees',[EmployeeController::class,'index'])->name('employee.index');
Route::post('/employee',[EmployeeController::class,'store'])->name('employee.save');
Route::put('/employee/{id}',[EmployeeController::class,'update'])->name('employee.update');
Route::delete('/employee/{id}',[EmployeeController::class,'destroy'])->name('employee.delete');

use App\Http\Controllers\TripController;
Route::get('/trips',[TripController::class,'index'])->name('trip.index');
Route::post('/trip',[TripController::class,'store'])->name('trip.store');
/* FILTER MUST BE BEFORE {id} ROUTES */
Route::get('/trip/filter',[TripController::class,'filter'])->name('trip.filter');
Route::get('/trip/{id}/edit',[TripController::class,'edit'])->name('trip.edit');
Route::put('/trip/{id}',[TripController::class,'update'])->name('trip.update');
Route::delete('/trip/{id}',[TripController::class,'destroy'])->name('trip.delete');
Route::get('/fetch-trips',[TripController::class,'fetchTrips'])->name('fetchTrips');
Route::get('/trip/pdf', [TripController::class,'downloadPDF'])->name('trip.pdf');


use App\Http\Controllers\ExpenseController;

Route::get('/expenses',[ExpenseController::class,'index'])->name('expense.index');

Route::post('/expense',[ExpenseController::class,'store'])->name('expense.store');

/* FILTER MUST BE BEFORE {id} ROUTES */
Route::get('/expense/filter',[ExpenseController::class,'filter'])->name('expense.filter');

Route::get('/expense/{id}/edit',[ExpenseController::class,'edit'])->name('expense.edit');

Route::put('/expense/{id}',[ExpenseController::class,'update'])->name('expense.update');

Route::delete('/expense/{id}',[ExpenseController::class,'destroy'])->name('expense.delete');

Route::get('/fetch-expenses',[ExpenseController::class,'fetchExpenses'])->name('expense.fetch');



use App\Http\Controllers\BillingController;

Route::prefix('billing')->group(function () {

    // Billing list
    Route::get('/display', [BillingController::class, 'display'])->name('billing.display');

    // Create invoice page
    Route::get('/', [BillingController::class, 'create'])->name('billing.create');

    // Filter trips
    Route::post('/filterTrips', [BillingController::class, 'filterTrips'])->name('billing.filterTrips');

    // Store invoice
    Route::post('/store', [BillingController::class, 'store'])->name('billing.store');

    // Add existing trips page
    Route::get('/{id}/addTrip', [BillingController::class, 'addTrip'])->name('billing.addTrip');

    // Add selected trips
    Route::post('/{id}/addTrips', [BillingController::class, 'addTrips'])->name('billing.addTrips');

    // CREATE MISSING TRIP FORM
    Route::get('/{id}/trip/create', [BillingController::class, 'createTrip'])->name('billing.trip.create');

    // STORE MISSING TRIP
    Route::post('/{id}/trip/store', [BillingController::class, 'storeTrip'])->name('billing.trip.store');

    // Edit invoice
    Route::get('/{id}/edit', [BillingController::class, 'edit'])->name('billing.edit');

    // Update billing item
    Route::post('/item/{id}/update', [BillingController::class, 'updateItem'])->name('billing.item.update');

    // Delete billing item
    Route::delete('/item/{id}', [BillingController::class, 'deleteItem'])->name('billing.item.delete');

});