<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Billing\BillingController;
use App\Http\Controllers\Billing\BillingFilterController;
use App\Http\Controllers\Billing\BillingInvoiceTripController;
use App\Http\Controllers\Billing\BillingInvoiceListingController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\WorkshopBillController;
use App\Http\Controllers\EmployeeAccountController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\SparepartCategoryController;
use App\Http\Controllers\SparepartSupplierController;
use App\Http\Controllers\SparepartStockController;
use App\Http\Controllers\DashboardAuthController;

/* =========================
   PUBLIC ROUTES
========================= */
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/dashboard-login', [DashboardAuthController::class, 'login'])->name('dashboard.login');
Route::post('/dashboard-logout', [DashboardAuthController::class, 'logout'])->name('dashboard.logout');

// Public image/file access
Route::get('/storage-file/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*');

/* =========================
   DRIVER ROUTES
========================= */
Route::get('/driver', [TripController::class, 'driverPage'])->name('driver.page');
Route::post('/driver/trip', [TripController::class, 'driverStore'])->name('driver.trip.store');

/* =========================
   PROTECTED DASHBOARD ROUTES
========================= */
Route::middleware(['dashboard.auth'])->group(function () {

    /* =========================
       DASHBOARD
    ========================= */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


Route::get('/dashboard/password', [DashboardAuthController::class, 'showChangePassword'])
    ->name('dashboard.password');

Route::post('/dashboard/password', [DashboardAuthController::class, 'updatePassword'])
    ->name('dashboard.password.update');


    /* =========================
       COMPANY
    ========================= */
    Route::get('/add-company', [CompanyController::class,'create'])->name('company.add');
    Route::post('/save-company', [CompanyController::class,'store'])->name('company.save');
    Route::put('/company/{id}', [CompanyController::class,'update'])->name('company.update');
    Route::delete('/company/{id}', [CompanyController::class,'destroy'])->name('company.delete');

    /* =========================
       DESTINATION
    ========================= */
    Route::get('/destinations',[DestinationController::class,'index'])->name('destination.index');
    Route::post('/destination',[DestinationController::class,'store'])->name('destination.save');
    Route::put('/destination/{id}',[DestinationController::class,'update'])->name('destination.update');
    Route::delete('/destination/{id}',[DestinationController::class,'destroy'])->name('destination.delete');

    /* =========================
       TRUCK
    ========================= */
    Route::get('/trucks',[TruckController::class,'index'])->name('truck.index');
    Route::post('/truck',[TruckController::class,'store'])->name('truck.save');
    Route::put('/truck/{id}',[TruckController::class,'update'])->name('truck.update');
    Route::delete('/truck/{id}',[TruckController::class,'destroy'])->name('truck.delete');

    /* =========================
       EMPLOYEE
    ========================= */
    Route::get('/employees',[EmployeeController::class,'index'])->name('employee.index');
    Route::post('/employee',[EmployeeController::class,'store'])->name('employee.save');
    Route::put('/employee/{id}',[EmployeeController::class,'update'])->name('employee.update');
    Route::delete('/employee/{id}',[EmployeeController::class,'destroy'])->name('employee.delete');

    /* =========================
       TRIPS
    ========================= */
    Route::get('/trips',[TripController::class,'index'])->name('trip.index');
    Route::post('/trip',[TripController::class,'store'])->name('trip.store');
    Route::get('/trip/filter',[TripController::class,'filter'])->name('trip.filter');
    Route::get('/trip/{id}/edit',[TripController::class,'edit'])->name('trip.edit');
    Route::put('/trip/{id}',[TripController::class,'update'])->name('trip.update');
    Route::delete('/trip/{id}',[TripController::class,'destroy'])->name('trip.delete');
    Route::get('/fetch-trips',[TripController::class,'fetchTrips'])->name('fetchTrips');
    Route::get('/trip/pdf', [TripController::class,'downloadPDF'])->name('trip.pdf');

    /* =========================
       EXPENSE
    ========================= */
    Route::get('/expenses',[ExpenseController::class,'index'])->name('expense.index');
    Route::post('/expense',[ExpenseController::class,'store'])->name('expense.store');
    Route::get('/expense/filter',[ExpenseController::class,'filter'])->name('expense.filter');
    Route::get('/expense/{id}/edit',[ExpenseController::class,'edit'])->name('expense.edit');
    Route::put('/expense/{id}',[ExpenseController::class,'update'])->name('expense.update');
    Route::delete('/expense/{id}',[ExpenseController::class,'destroy'])->name('expense.delete');
    Route::get('/fetch-expenses',[ExpenseController::class,'fetchExpenses'])->name('expense.fetch');

    /* =========================
       BILLING
    ========================= */
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'create'])->name('create');
        Route::post('/', [BillingInvoiceTripController::class, 'BillingStore'])->name('store');
        Route::delete('/item/{id}', [BillingInvoiceTripController::class, 'BillingDelete'])->name('item.delete');
        Route::post('/filter-trips', [BillingFilterController::class, 'filterTrips'])->name('filterTrips');
        Route::get('/list', [BillingInvoiceListingController::class, 'index'])->name('index');
        Route::get('/filter', [BillingInvoiceListingController::class, 'filter'])->name('filter');
        Route::post('/mark-paid/{id}', [BillingInvoiceListingController::class, 'markPaid'])->name('markPaid');
        Route::delete('/delete/{id}', [BillingInvoiceListingController::class, 'deleteInvoice'])->name('delete');
        Route::delete('/delete-item/{id}', [BillingInvoiceListingController::class, 'deleteItem'])->name('item.delete.ajax');
        Route::get('/print/{id}', [BillingInvoiceListingController::class, 'print'])->name('print');
        Route::post('/update-item/{id}', [BillingInvoiceListingController::class, 'updateItem'])->name('item.update');
    });

    /* =========================
       PURPOSE
    ========================= */
    Route::get('/purposes', [PurposeController::class,'index'])->name('purpose.index');
    Route::get('/add-purpose', [PurposeController::class,'create'])->name('purpose.add');
    Route::post('/save-purpose', [PurposeController::class,'store'])->name('purpose.save');
    Route::put('/purpose/{id}', [PurposeController::class,'update'])->name('purpose.update');
    Route::delete('/purpose/{id}', [PurposeController::class,'destroy'])->name('purpose.delete');

    /* =========================
       SUGGESTION
    ========================= */
    Route::get('/suggestions', [SuggestionController::class,'index'])->name('suggestion.index');
    Route::get('/add-suggestion', [SuggestionController::class,'create'])->name('suggestion.add');
    Route::post('/save-suggestion', [SuggestionController::class,'store'])->name('suggestion.save');
    Route::put('/suggestion/{id}', [SuggestionController::class,'update'])->name('suggestion.update');
    Route::delete('/suggestion/{id}', [SuggestionController::class,'destroy'])->name('suggestion.delete');

    /* =========================
       ACCOUNTS
    ========================= */
    Route::get('/accounts', [AccountController::class,'index'])->name('accounts.index');
    Route::post('/accounts', [AccountController::class,'store'])->name('accounts.store');
    Route::delete('/accounts/{id}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::get('/accounts/pdf', [AccountController::class, 'exportPdf'])->name('accounts.pdf');

    /* =========================
       WORKSHOP
    ========================= */
    Route::get('/workshop/create', [WorkshopBillController::class, 'create'])->name('workshop.create');
    Route::post('/workshop/store', [WorkshopBillController::class, 'store'])->name('workshop.store');
    Route::delete('/workshop/delete/{id}', [WorkshopBillController::class, 'destroy']);
    Route::get('/workshop/edit/{id}', [WorkshopBillController::class, 'edit']);
    Route::post('/workshop/mark-paid/{id}', [WorkshopBillController::class, 'markPaid']);
    Route::delete('/workshop/item/delete/{id}', [WorkshopBillController::class, 'deleteItem']);
    Route::get('/workshop/item/edit/{id}', [WorkshopBillController::class, 'editItem']);
    Route::post('/workshop/item/update/{id}', [WorkshopBillController::class, 'updateItem']);
    Route::get('/workshop/pdf/{id}', [WorkshopBillController::class, 'generatePDF']);
    Route::get('/item-suggestions', [WorkshopBillController::class, 'itemSuggestions']);

    /* =========================
       EMPLOYEE ACCOUNTS
    ========================= */
    Route::get('/employee-accounts', [EmployeeAccountController::class, 'index'])->name('employee.accounts');
    Route::post('/employee-accounts', [EmployeeAccountController::class, 'store']);
    Route::get('/employee-accounts/filter', [EmployeeAccountController::class, 'filter']);
    Route::delete('/employee-accounts/{id}', [EmployeeAccountController::class, 'destroy']);
    Route::get('/employee-accounts/pdf', [EmployeeAccountController::class, 'pdf']);

    /* =========================
       SPARE PART
    ========================= */
    Route::get('/sparepart', [SparepartController::class,'index'])->name('sparepart.index');
    Route::post('/sparepart', [SparepartController::class,'store']);
    Route::put('/sparepart/{id}', [SparepartController::class,'update']);
    Route::delete('/sparepart/{id}', [SparepartController::class,'destroy']);

    Route::post('/sparepart-category', [SparepartCategoryController::class,'store']);
    Route::put('/sparepart-category/{id}', [SparepartCategoryController::class,'update']);
    Route::delete('/sparepart-category/{id}', [SparepartCategoryController::class,'destroy']);

    Route::post('/sparepart-supplier', [SparepartSupplierController::class,'store']);

    Route::post('/sparepart-stock', [SparepartStockController::class,'store']);
    Route::get('/sparepart-stock/{id}', [SparepartStockController::class,'history']);
});
