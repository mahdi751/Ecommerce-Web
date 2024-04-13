<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BuyerController;
use \UniSharp\LaravelFilemanager\Lfm;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

// Route::get('/home', [FrontendController::class, 'index']);
Route::get('user/logout', [\App\Http\Controllers\SellerController::class, 'logout'])->name('user.logout');

//Route::get('SellerLogin', [SellerController::class, 'login'])->name('seller.login');

Route::group(['prefix' => '/seller', 'middleware' => ['auth', 'seller']], function () {

    Route::get('/', [\App\Http\Controllers\SellerController::class, 'index'])->name('seller');

    Route::get('/file-manager', function () {
        return view('Sellers.layouts.file-manager');
    })->name('file-manager');

    Route::get('/store', [\App\Http\Controllers\StoreController::class, 'index'])->name('store.index');
    Route::resource('/store', '\App\Http\Controllers\StoreController');


    Route::resource('/category', '\App\Http\Controllers\CategoryController');
    Route::post('/category/{id}/child', '\App\Http\Controllers\CategoryController@getChildByParent');

    Route::resource('/product', '\App\Http\Controllers\ProductController');

    Route::resource('/message', '\App\Http\Controllers\MessagesController');
    Route::get('/message/five', [\App\Http\Controllers\MessagesController::class, 'messageFive'])->name('messages.five');

    Route::resource('/order', '\App\Http\Controllers\OrderController');

    Route::resource('/shipping', '\App\Http\Controllers\ShippingController');

    Route::resource('/coupon', '\App\Http\Controllers\CouponsController');

    Route::get('/notification/{id}', [\App\Http\Controllers\NotificationController::class, 'show'])->name('seller.notification');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('all.notification');
    Route::delete('/notification/{id}', [\App\Http\Controllers\NotificationController::class, 'delete'])->name('notification.delete');

    Route::get('/profile', [\App\Http\Controllers\SellerController::class, 'profile'])->name('seller-profile');
    Route::post('/profile/{id}', [\App\Http\Controllers\SellerController::class, 'profileUpdate'])->name('profile-update');

    Route::get('change-password', [\App\Http\Controllers\SellerController::class, 'changePassword'])->name('change.password.form');
    Route::post('change-password', [\App\Http\Controllers\SellerController::class, 'changPasswordRequest'])->name('change.password');
});

Route::group(['prefix' => '/user', 'middleware' => ['user']], function () {
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('user');
});

Route::get('/income', [OrderController::class, 'incomeChart'])->name('product.order.income');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/home/{store_id}', [App\Http\Controllers\BuyerController::class, 'home'])->name('homestore');
Route::post('/product/search', [BuyerController::class, 'productSearch'])->name('product.search');


Route::get('/about-us', [BuyerController::class, 'aboutUs'])->name('about-us');
    Route::get('/contact', [BuyerController::class, 'contact'])->name('contact');
    Route::post('/contact/message', [BuyerController::class, 'store'])->name('contact.store');
    Route::get('product-detail/{slug}', [BuyerController::class, 'productDetail'])->name('product-detail');
    Route::post('/product/search', [BuyerController::class, 'productSearch'])->name('product.search');
    Route::get('/product-cat/{slug}', [BuyerController::class, 'productCat'])->name('product-cat');
    Route::get('/product-sub-cat/{slug}/{sub_slug}', [BuyerController::class, 'productSubCat'])->name('product-sub-cat');
    Route::get('/product-grids', [BuyerController::class, 'productGrids'])->name('product-grids');
    Route::get('/product-lists', [BuyerController::class, 'productLists'])->name('product-lists');


//Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () { '\vendor\UniSharp\LaravelFilemanager\Lfm::routes()'; });


Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    Lfm::routes();
});     


Route::group(['prefix' => '/admin', 'middleware' => ['auth', 'admin']], function () {

    Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin');
    Route::get('/profile', [\App\Http\Controllers\AdminController::class, 'profile'])->name('admin-profile');
    Route::post('/profile/{id}', [\App\Http\Controllers\AdminController::class, 'profileUpdate'])->name('profile-update');

    Route::get('/stores', [\App\Http\Controllers\StoreController::class, 'stores'])->name('store.stores');
    Route::get('/stores/{id}', [\App\Http\Controllers\StoreController::class, 'editAdmin'])->name('store.editAdmin');
    Route::patch('/stores/{id}', [\App\Http\Controllers\StoreController::class, 'updateAdmin'])->name('store.updateAdmin');

    Route::resource('users', '\App\Http\Controllers\UserController');
});
// Product Review
Route::resource('/review', '\App\Http\Controllers\ProductReviewController');
Route::post('product/{slug}/review', [ProductReviewController::class, 'store'])->name('review.store');

