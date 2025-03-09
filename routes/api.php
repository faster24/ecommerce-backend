<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {
});

Route::prefix('products')->controller(ProductController::class)->group(function () {
    Route::get('/', 'index'); // List all products
    Route::get('/search', 'search'); // Search products (placed before show)
    Route::get('/{id}', 'show'); // Show a specific product by numeric ID
});

Route::prefix('order')->controller(OrderController::class)->group(function() {
    Route::post('/buyOrder', 'buyOrder');
    Route::get('/history', 'orderHistory');
});

Route::group(['prefix' => 'brands'], function () {
    Route::get('/', [BrandController::class, 'index']);
});

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [CustomerController::class, 'getProfile']);
    Route::put('/profile', [CustomerController::class, 'updateProfile']);
});
