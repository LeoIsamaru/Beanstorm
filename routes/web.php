<?php

use App\Http\Controllers\BrandsController;
use App\Http\Controllers\CoffeesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilController;
use Illuminate\Support\Facades\Route;

//=================================== 1. Home & Others ===================================

// Homepage route
Route::get('/', [UtilController::class, 'loadHomepage'])->name('home');
Route::get('/home', [UtilController::class, 'loadHomepage'])->name('home.alias');
Route::get('/welcome', [UtilController::class, 'loadWelcome'])->name('welcome');

//========================================================================================


//=================================== 2. CoffeesController ===============================

// All coffees route (public - viewable by everyone)
Route::get('/coffees', [CoffeesController::class, 'allCoffees'])->name('coffees.all');

// View/edit coffee route (public - displays coffee details and brands)
Route::get('/edit-coffees/{id}', [CoffeesController::class, 'editCoffees'])->name('coffees.edit');

// Authenticated coffee routes
Route::middleware('auth')->group(function () {
    // Add coffee form (Admin only)
    Route::get('/addcoffees', [CoffeesController::class, 'addCoffees'])->name('coffees.add');

    // Store new coffee (Admin only)
    Route::post('/store-coffees', [CoffeesController::class, 'storeCoffees'])->name('coffees.store');

    // Update coffee (Admin or coffee owner)
    Route::put('/updatecoffees', [CoffeesController::class, 'updateCoffees'])->name('coffees.update');

    // Delete coffee (Admin only)
    Route::get('/delete-coffees/{id}', [CoffeesController::class, 'deleteCoffees'])->name('coffees.delete');
});

//========================================================================================


//=================================== 3. BrandsController ================================

// Authenticated brand routes (Admin only)
Route::middleware('auth')->group(function () {
    // Store new brand under a coffee bean
    Route::post('/store-brand', [BrandsController::class, 'store'])->name('brands.store');

    // Update brand
    Route::put('/update-brand', [BrandsController::class, 'update'])->name('brands.update');

    // Delete brand
    Route::get('/delete-brand/{id}', [BrandsController::class, 'delete'])->name('brands.delete');
});

//========================================================================================


//=================================== 4. UserController ==================================

// Add user/register form route (Public)
Route::get('/addusers', [UserController::class, 'addUsers'])->name('users.add');

// Store new user/register submission (Public)
Route::post('/store-users', [UserController::class, 'storeUser'])->name('users.store');

// Authenticated user management routes
Route::middleware('auth')->group(function () {
    // All users route (viewable by authenticated users)
    Route::get('/users', [UserController::class, 'allUsers'])->name('users.all');

    // Delete user route (Admin only)
    Route::get('/delete-users/{id}', [UserController::class, 'deleteUsers'])->name('users.delete');

    // View/edit user profile
    Route::get('/edit-users/{id}', [UserController::class, 'editUsers'])->name('users.edit');

    // Update user profile
    Route::put('/update-user', [UserController::class, 'updateuser'])->name('users.update');
});

//========================================================================================


//=================================== 5. Dashboard =======================================

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('auth');

//========================================================================================


//=================================== 6. Fallback ========================================

// Fallback route - placed at the end to catch any unmatched URLs
Route::fallback(function () {
    return '<h1>No coffee here! Grind some beans Back!</h1>';
});

//========================================================================================
