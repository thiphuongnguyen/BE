<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CouponController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Product
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);
Route::get('/products/search', [ProductController::class, 'searchByName']);
Route::get('/products/latest', [ProductController::class, 'getLatestProducts']);
Route::get('/products/random', [ProductController::class, 'getRandomEightProducts']);
Route::get('/products/{category_id}', [ProductController::class, 'getProductsByCategory']);
Route::get('/products/detail/{product_id}', [ProductController::class, 'getProductDetail']);
Route::get('/products/related/{category_id}/{product_id}', [ProductController::class, 'getFourProductsByCategory']);
Route::get('/allproducts', [ProductController::class, 'getAllProductsExceptInactiveCategories']);
Route::put('/products/updateStatus/{product}', [ProductController::class, 'updateProductStatus']);


// Danh sách tất cả sliders
Route::get('/sliders', [SliderController::class, 'index']);
// Lưu slider mới
Route::post('/sliders', [SliderController::class, 'store']);
// Cập nhật thông tin của một slider cụ thể
Route::put('/sliders/{slider}', [SliderController::class, 'update']);
// Xóa một slider cụ thể
Route::delete('/sliders/{slider}', [SliderController::class, 'destroy']);

// Order
Route::get('/order', [OrderController::class, 'index']);
Route::post('/order', [OrderController::class, 'store']);
Route::get('/order/{customer_id}', [OrderController::class, 'getOrdersByCustomerId']);
Route::put('/order/{order_id}', [OrderController::class, 'update']);

//Cart
Route::post('/carts', [CartController::class, 'store']);
Route::get('/cart/{customer_id}', [CartController::class, 'getCartProducts']);
Route::delete('/cart/customer/{customer_id}/product/{product_id}', [CartController::class, 'deleteProductFromCart']);
Route::delete('/cart/customer/{customer_id}', [CartController::class, 'deleteAllProductsFromCart']);
Route::put('/cart/customer/{customer_id}', [CartController::class, 'update']);

// Customer
Route::post('/customer', [CustomerController::class, 'store']);
Route::post('/customer/login', [CustomerController::class, 'login']);
Route::get('/customer', [CustomerController::class, 'index']);
Route::put('/customer/{customer}', [CustomerController::class, 'update']);
Route::delete('/customer/{customer}', [CustomerController::class, 'destroy']);
Route::get('/customer/detail/{customer}', [CustomerController::class, 'getCustomerDetail']);

// Category
Route::get('/category', [CategoryController::class, 'index']);
Route::delete('/category/{category}', [CategoryController::class, 'destroy']);
Route::put('/category/{category}', [CategoryController::class, 'update']);
Route::post('/category', [CategoryController::class, 'store']);

// Address
Route::get('/get-provinces', [AddressController::class, 'getProvinces']);
Route::get('/get-districts/{provinceId}', [AddressController::class, 'getDistricts']);
Route::get('/get-wards/{districtId}', [AddressController::class, 'getWards']);

// Galleries
Route::get('/galleries/{product_id}', [GalleryController::class, 'index']);
Route::post('/galleries/multiple', [GalleryController::class, 'store']);
Route::delete('/galleries/{gallery}', [GalleryController::class, 'destroy']);

// Admin
Route::post('/admin', [AdminController::class, 'store']);
Route::post('/admin/login', [AdminController::class, 'login']);

// News
Route::get('/news', [NewsController::class, 'index']);
Route::post('/news', [NewsController::class, 'store']);
Route::put('/news/{news}', [NewsController::class, 'update']);
Route::delete('/news/{news}', [NewsController::class, 'destroy']);
Route::get('/news/{news_id}', [NewsController::class, 'getNewsDetail']);
Route::get('/allNews', [NewsController::class, 'getAllNewsInactive']);
Route::put('/news/updateStatus/{news}', [NewsController::class, 'updateNewsStatus']);

// Coupon
Route::get('/coupon', [CouponController::class, 'index']);
Route::post('/coupon', [CouponController::class, 'store']);
Route::put('/coupon/{coupon}', [CouponController::class, 'update']);
Route::delete('/coupon/{coupon}', [CouponController::class, 'destroy']);
Route::get('/allCoupon', [CouponController::class, 'getAllCouponInactive']);